<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);
        $includeRead = $request->get('include_read', true);

        $notifications = $this->notificationService->getUserNotifications(
            $user,
            $limit,
            $includeRead
        );

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $this->notificationService->getUnreadCount($user),
        ]);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->getUnreadCount($user);

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $id): JsonResponse
    {
        $user = Auth::user();
        $success = $this->notificationService->markAsRead($id, $user);

        if (!$success) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->markAllAsRead($user);

        return response()->json([
            'message' => 'All notifications marked as read',
            'count' => $count,
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();
        $success = $this->notificationService->deleteNotification($id, $user);

        if (!$success) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * Clear all notifications.
     */
    public function clearAll(): JsonResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->clearAllNotifications($user);

        return response()->json([
            'message' => 'All notifications cleared',
            'count' => $count,
        ]);
    }

    /**
     * Get notifications by type.
     */
    public function byType(Request $request, string $type): JsonResponse
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);

        $notifications = $this->notificationService->getNotificationsByType(
            $user,
            $type,
            $limit
        );

        return response()->json(['notifications' => $notifications]);
    }

    /**
     * Get notification types.
     */
    public function types(): JsonResponse
    {
        $types = $this->notificationService->getActiveNotificationTypes();

        return response()->json(['types' => $types]);
    }

    /**
     * Create a test notification (for development/testing).
     */
    public function createTest(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $request->validate([
            'type' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
            'severity' => 'string|in:info,success,warning,error',
        ]);

        try {
            $notification = $this->notificationService->createNotification(
                $user,
                $request->type,
                $request->title,
                $request->message,
                $request->severity ?? 'info',
                $request->only(['action_label', 'action_url', 'action_data'])
            );

            return response()->json([
                'message' => 'Test notification created',
                'notification' => $notification->load('notificationType'),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
