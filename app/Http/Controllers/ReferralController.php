<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserReferralW9;
use App\Models\UserReferralEarning;
use App\Services\ReferralEarningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Models\AwsS3History;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $earningService = new ReferralEarningService();

        $affiliateQuery = User::where('affiliated_user', $user->id);
        $totalCount = (clone $affiliateQuery)->count();

        $affiliateEligible = (clone $affiliateQuery)
            ->where('user_type', '!=', User::FREE)
            ->get();

        $affiliateEligibleCount = $affiliateEligible->count();

        $userLink = route('referral-link', ['uuid' => $user->uuid]);
        $w9s = UserReferralW9::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        // Get earnings data
        $earnings = UserReferralEarning::where('user_id', $user->id)
            ->with('fromUser')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($earning) {
                return [
                    'id' => $earning->id,
                    'from' => $earning->fromUser ? $earning->fromUser->name : 'Unknown',
                    'amount' => $earning->amount,
                    'status' => $earning->status,
                    'type' => $earning->type,
                    'description' => $earning->description,
                    'created_at' => $earning->created_at,
                    'updated_at' => $earning->updated_at,
                    'paid_at' => $earning->paid_at,
                ];
            });

        $earningsSummary = $earningService->getEarningsSummary($user);

        return Inertia::render('referral/ReferralPage', [
            'total_affiliates' => $totalCount,
            'eligibleAffiliate' => $affiliateEligible,
            'eligibleAffiliateCount' => $affiliateEligibleCount,
            'earnings' => $earnings,
            'totalEarning' => $earningsSummary['total_earnings'],
            'pendingEarnings' => $earningsSummary['pending_earnings'],
            'approvedEarnings' => $earningsSummary['approved_earnings'],
            'paidEarnings' => $earningsSummary['paid_earnings'],
            'canPayout' => $earningsSummary['can_payout'],
            'minPayoutThreshold' => $earningsSummary['min_payout_threshold'],
            'referralLink' => $userLink,
            'mustVerifyEmail' => !$user->hasVerifiedEmail(),
            'status' => $request->session()->get('status'),
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
            'w9s' => $w9s,
        ]);
    }


    public function referralLink(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->first();
        if (!$user) {
            return redirect()->route('referrals')->with('error', 'User not found');
        }

        Session::put('referral_user', $user->uuid);
        Cookie::queue('referral_user', $user->uuid, 60 * 24 * 30); // 30 days

        $request->session()->regenerate();

        return redirect()->route('register')->with('success', 'User found');
    }

    public function uploadW9(Request $request)
    {
        $request->validate([
            'w9File' => 'required|file|mimes:pdf',
        ]);

        if ($request->hasFile('w9File')) {
            $file = $request->file('w9File');
            try {
                $path = "w9/";
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $fullPath = $path . $filename;

                Storage::disk('s3')->putFileAs($path, $file, $filename, 'public');

                $url = Storage::disk('s3')->url($fullPath);
                AwsS3History::addHistory('w9-files', $fullPath, $url, $request->user()->id);
                if ($url) {
                    UserReferralW9::create([
                        'name' => $file->getClientOriginalName(),
                        'file_url' => $url,
                        'remarks' => null,
                        'approved' => false,
                        'user_id' => $request->user()->id,
                    ]);
                    return response()->json(['success' => true, 'message' => 'W9 file has been uploaded!', 'path' => $url]);
                }
            } catch (\Exception $e) {
                AwsS3History::addHistory('w9-files', null, null, $request->user()->id, $e->getMessage());
            }

            return response()->json(['success' => false, 'error' => 'Could not be upload your file.']);
        }


        return response()->json(['success' => false, 'error' => 'Could not be upload your file.']);
    }

    public function adminReferralsIndex(Request $request)
    {
        $query = User::whereNotNull('affiliate_slug')
            ->withCount(['affiliates', 'eligibleAffiliates'])
            ->withSum('earnings', 'amount')
            ->withSum('earnings as total_earnings', 'amount');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // User type filter
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $referrals = $query->paginate(15);

        return Inertia::render('admin-pages/referrals/AdminReferralsIndex', [
            'referrals' => $referrals,
        ]);
    }

    public function adminReferralsW9s(Request $request)
    {
        $query = UserReferralW9::with('user')
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('approved', false);
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'user_name') {
            $query->join('users', 'user_referral_w9s.user_id', '=', 'users.id')
                  ->orderBy('users.name', $sortOrder)
                  ->select('user_referral_w9s.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $w9s = $query->paginate(15);

        return Inertia::render('admin-pages/referrals/AdminReferralsW9s', [
            'w9s' => $w9s,
        ]);
    }

    public function adminReferralsEarnings(Request $request)
    {
        $query = UserReferralEarning::with(['user', 'fromUser'])
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('fromUser', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $earnings = $query->paginate(15);

        return Inertia::render('admin-pages/referrals/AdminReferralsEarnings', [
            'earnings' => $earnings,
        ]);
    }

    public function approveW9(Request $request)
    {
        $request->validate([
            'w9_id' => 'required|exists:user_referral_w9s,id',
            'approved' => 'required|boolean',
            'remarks' => 'nullable|string|max:500',
        ]);

        $w9 = UserReferralW9::findOrFail($request->w9_id);
        $w9->update([
            'approved' => $request->approved,
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'W9 document status updated successfully');
    }

    public function approveEarning(Request $request)
    {
        $request->validate([
            'earning_id' => 'required|exists:user_referral_earnings,id',
            'approved' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $earning = UserReferralEarning::findOrFail($request->earning_id);
        
        if ($request->approved) {
            $earning->approve();
            $message = 'Earning approved successfully';
        } else {
            $earning->reject($request->notes);
            $message = 'Earning rejected successfully';
        }

        return back()->with('success', $message);
    }

    public function payEarning(Request $request)
    {
        $request->validate([
            'earning_id' => 'required|exists:user_referral_earnings,id',
        ]);

        $earning = UserReferralEarning::findOrFail($request->earning_id);
        
        if ($earning->status !== UserReferralEarning::STATUS_APPROVED) {
            return back()->with('error', 'Only approved earnings can be marked as paid');
        }

        $earning->markAsPaid();

        return back()->with('success', 'Earning marked as paid successfully');
    }

    public function bulkApproveEarnings(Request $request)
    {
        $request->validate([
            'earning_ids' => 'required|array',
            'earning_ids.*' => 'exists:user_referral_earnings,id',
        ]);

        $earningService = new ReferralEarningService();
        $count = $earningService->approveEarnings($request->earning_ids);

        return back()->with('success', "{$count} earnings approved successfully");
    }

    public function bulkPayEarnings(Request $request)
    {
        $request->validate([
            'earning_ids' => 'required|array',
            'earning_ids.*' => 'exists:user_referral_earnings,id',
        ]);

        $earningService = new ReferralEarningService();
        $count = $earningService->payEarnings($request->earning_ids);

        return back()->with('success', "{$count} earnings marked as paid successfully");
    }
}
