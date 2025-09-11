<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\AwsS3History;
use App\Services\EmailService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of support tickets (Admin)
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with([
            'user',
            'closedBy',
            'replies' => function ($q) {
                $q->latest()->limit(1);
            }
        ]);

        // Search by ticket number, title, or user
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%$search%")
                    ->orWhere('title', 'like', "%$search%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        // Filter by category
        if ($request->filled('category')) {
            $category = $request->input('category');
            $query->whereJsonContains('categories', $category);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        if ($sortBy === 'ticket_number') {
            $query->orderBy('ticket_number', $sortDir);
        } elseif ($sortBy === 'title') {
            $query->orderBy('title', $sortDir);
        } elseif ($sortBy === 'priority') {
            $query->orderBy('priority', $sortDir);
        } elseif ($sortBy === 'status') {
            $query->orderBy('status', $sortDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $supportTickets = $query->paginate(perPage: 10)->withQueryString();

        return Inertia::render('admin-pages/support-tickets/SupportTickets', [
            'supportTickets' => $supportTickets,
        ]);
    }

    /**
     * Display the specified support ticket (Admin)
     */
    public function show($id)
    {
        $supportTicket = SupportTicket::with([
            'user',
            'closedBy',
            'replies.user'
        ])->findOrFail($id);

        return Inertia::render('admin-pages/support-tickets/SupportTicketDetail', [
            'supportTicket' => $supportTicket,
        ]);
    }

    /**
     * Update support ticket status (Admin)
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $supportTicket = SupportTicket::findOrFail($id);

        $request->validate([
            'status' => 'required|in:open,in_progress,waiting_for_user,waiting_for_admin,closed',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $supportTicket->update([
            'status' => $request->status,
            'priority' => $request->priority,
        ]);

        if ($request->status === 'closed') {
            $supportTicket->close(Auth::id());
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket status updated successfully'
        ]);
    }

    /**
     * Add reply to support ticket (Admin)
     */
    public function addReply(Request $request, $id): JsonResponse
    {
        $supportTicket = SupportTicket::findOrFail($id);

        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|image|max:15000',
        ]);

        $attachments = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                try {
                    $imageManager = new ImageManager(new Driver());
                    $image = $imageManager->read($file->getPathname());
                    $resizedImage = $image->resizeDown(1500, 1500, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->toJpeg(90);

                    $path = 'support-tickets/' . $supportTicket->id . '/replies/' . uniqid() . '.jpg';
                    Storage::disk('s3')->put($path, (string) $resizedImage, 'public');
                    $url = Storage::disk('s3')->url($path);

                    AwsS3History::addHistory('support-ticket-reply', $path, $url, $supportTicket->id);
                    $attachments[] = $url;
                } catch (\Exception $e) {
                    AwsS3History::addHistory('support-ticket-reply', null, null, $supportTicket->id, $e->getMessage());
                }
            }
        }

        $reply = SupportTicketReply::create([
            'support_ticket_id' => $supportTicket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachments' => $attachments,
            'is_admin_reply' => true,
        ]);

        $supportTicket->update(['status' => 'waiting_for_user']);

        $this->sendEmailToUser($supportTicket, 'support_ticket_reply', [
            'subject' => 'Revamp365 - Ticket #' . $supportTicket->ticket_number,
            'body' => 'Admin reply added by ' . Auth::user()->name . ' on ' . now()->format('d/m/Y H:i'),
            'message' => $request->message,
        ]);


        app(NotificationService::class)->createNotification(
            $supportTicket->user,
            'support-ticket',
            'Admin reply added',
            $request->message,
            'info',
            [
                'action_label' => 'View Ticket',
                'action_url' => route('support.tickets.show', $supportTicket->id),
            ]
        );


        return response()->json([
            'success' => true,
            'message' => 'Reply added successfully',
            'reply' => $reply->load('user'),
        ]);
    }

    /**
     * Close support ticket (Admin)
     */
    public function close($id): JsonResponse
    {
        $supportTicket = SupportTicket::findOrFail($id);
        $supportTicket->close(Auth::id());

        $this->sendEmailToUser($supportTicket, 'support_ticket_closed', [
            'subject' => 'Revamp365 - Ticket #' . $supportTicket->ticket_number,
            'body' => 'Ticket closed by admin on ' . now()->format('d/m/Y H:i'),
            'message' => '',
        ]);



        app(NotificationService::class)->createNotification(
            $supportTicket->user,
            'support-ticket',
            'Ticket closed by admin',
            '',
            'info',
            [
                'action_label' => 'View Ticket',
                'action_url' => route('support.tickets.show', $supportTicket->id),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Ticket closed successfully'
        ]);
    }

    /**
     * Reopen support ticket (Admin)
     */
    public function reopen($id): JsonResponse
    {
        $supportTicket = SupportTicket::findOrFail($id);
        $supportTicket->reopen();

        return response()->json([
            'success' => true,
            'message' => 'Ticket reopened successfully'
        ]);
    }

    /**
     * Get support ticket categories
     */
    public function getCategories(): JsonResponse
    {
        $categories = [
            'Account',
            'Property',
            'Wholesale (My Property)',
            'Email Marketing',
            'Payment',
            'Subscription',
            'Other'
        ];

        return response()->json($categories);
    }

    // User-side methods (for future implementation)

    /**
     * Display user's support tickets
     */
    public function userTickets(Request $request)
    {
        $query = SupportTicket::where('user_id', Auth::id())
            ->with([
                'replies' => function ($q) {
                    $q->latest()->limit(1);
                }
            ]);

        // Search by ticket number or title
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%$search%")
                    ->orWhere('title', 'like', "%$search%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $supportTickets = $query->orderBy('created_at', 'desc')
            ->paginate(perPage: 10)
            ->withQueryString();

        return Inertia::render('support-tickets/UserTickets', [
            'supportTickets' => $supportTickets,
        ]);
    }

    /**
     * Show user's specific ticket
     */
    public function userShow($id)
    {
        $supportTicket = SupportTicket::with(['replies.user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('support-tickets/UserTicketDetail', [
            'supportTicket' => $supportTicket,
        ]);
    }

    /**
     * Create new support ticket (User)
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'categories' => 'required|array|min:1',
            'categories.*' => 'string',
            'attachments.*' => 'nullable|image|max:15000',
        ]);

        $attachments = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                try {
                    $imageManager = new ImageManager(new Driver());
                    $image = $imageManager->read($file->getPathname());
                    $resizedImage = $image->resizeDown(1500, 1500, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->toJpeg(90);

                    $path = 'support-tickets/attachments/' . uniqid() . '.jpg';
                    Storage::disk('s3')->put($path, (string) $resizedImage, 'public');
                    $url = Storage::disk('s3')->url($path);

                    AwsS3History::addHistory('support-ticket-attachment', $path, $url, Auth::id());
                    $attachments[] = $url;
                } catch (\Exception $e) {
                    AwsS3History::addHistory('support-ticket-attachment', null, null, Auth::id(), $e->getMessage());
                }
            }
        }

        $supportTicket = SupportTicket::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'categories' => $request->categories,
            'attachments' => $attachments,
        ]);

        $this->sendEmailToAdmin($supportTicket, 'new_support_ticket', [
            'subject' => 'Revamp365 - Ticket #' . $supportTicket->ticket_number,
            'body' => 'New support ticket created by ' . Auth::user()->name . ' on ' . now()->format('d/m/Y H:i'),
            'message' => $request->description,
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Support ticket created successfully',
            'ticket' => $supportTicket,
        ]);
    }

    /**
     * Add user reply to support ticket
     */
    public function userReply(Request $request, $id): JsonResponse
    {
        $supportTicket = SupportTicket::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|image|max:15000',
        ]);

        $attachments = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                try {
                    $imageManager = new ImageManager(new Driver());
                    $image = $imageManager->read($file->getPathname());
                    $resizedImage = $image->resizeDown(1500, 1500, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->toJpeg(90);

                    $path = 'support-tickets/' . $supportTicket->id . '/replies/' . uniqid() . '.jpg';
                    Storage::disk('s3')->put($path, (string) $resizedImage, 'public');
                    $url = Storage::disk('s3')->url($path);

                    AwsS3History::addHistory('support-ticket-reply', $path, $url, $supportTicket->id);
                    $attachments[] = $url;
                } catch (\Exception $e) {
                    AwsS3History::addHistory('support-ticket-reply', null, null, $supportTicket->id, $e->getMessage());
                }
            }
        }

        $reply = SupportTicketReply::create([
            'support_ticket_id' => $supportTicket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachments' => $attachments,
            'is_admin_reply' => false,
        ]);

        // Update ticket status to waiting_for_admin (waiting for admin to respond)
        $supportTicket->update(['status' => 'waiting_for_admin']);

        $this->sendEmailToAdmin($supportTicket, 'support_ticket_user_reply', [
            'subject' => 'Revamp365 - Ticket #' . $supportTicket->ticket_number,
            'body' => 'New reply added by ' . Auth::user()->name . ' on ' . now()->format('d/m/Y H:i'),
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply added successfully',
            'reply' => $reply->load('user'),
        ]);
    }

    /**
     * Close support ticket (User)
     */
    public function userClose($id): JsonResponse
    {
        $supportTicket = SupportTicket::where('user_id', Auth::id())->findOrFail($id);
        $supportTicket->close(Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Ticket closed successfully'
        ]);
    }


    public function sendEmailToAdmin($supportTicket, $type, $data = [])
    {

        $url = route('admin.support-tickets.show', $supportTicket->id);
        $body = <<<HTML
        <p>{$data['body']}</p>
        <p>Ticket Number: {$supportTicket->ticket_number}</p>
        <p>Ticket Title: {$supportTicket->title}</p>
        <p>User Name: {$supportTicket->user->name} ({$supportTicket->user->email})</p>
        <p>User Message: {$data['message']}</p>
        <a href="{$url}">View Ticket</a>
        HTML;

        //  Send email notification to admin
        try {
            $emailService = app(EmailService::class);
            $emailService->sendCustom(
                config('mail.admin_email'),
                'Revamp365 Support',
                $type,
                config('mail.templates.' . $type),
                ['subject' => $data['subject'], 'body' => $body]
            );
        } catch (\Exception $e) {
            \Log::error('Support ticket admin reply email error: ' . $e->getMessage());
        }
    }


    public function sendEmailToUser($supportTicket, $type, $data = [])
    {
        $url = route('support.tickets.show', $supportTicket->id);
        $body = <<<HTML
        <p>{$data['body']}</p>
        <p>Ticket Number: {$supportTicket->ticket_number}</p>
        <p>Ticket Title: {$supportTicket->title}</p>
        <p>Admin Message: {$data['message']}</p>
        <a href="{$url}">View Ticket</a>
        HTML;

        //  Send email notification to user
        try {
            $emailService = app(EmailService::class);
            $emailService->sendCustom(
                $supportTicket->user->email,
                $supportTicket->user->name,
                $type,
                config('mail.templates.' . $type),
                ['subject' => $data['subject'], 'body' => $body]
            );
        } catch (\Exception $e) {
            \Log::error('Support ticket user reply email error: ' . $e->getMessage());
        }
    }
}