<?php

namespace App\Http\Controllers;

use App\Helper\LocationHelper;
use App\Helper\PropertyHelper;
use App\Models\AwsS3History;
use App\Models\Campaign;
use App\Models\PropertyFilterHistory;
use App\Models\PropertyViewHistory;
use App\Models\UpgradeFeature;
use App\Models\CountyState;
use App\Models\RequestAShowing;
use App\Models\AskQuestion;
use App\Models\ContactUsRequest;
use App\Models\InstantOffer;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\User;
use App\Models\LeadSource;
use App\Http\Controllers\SupportTicketController;
use App\Models\MyBuyerList;
use App\Models\MyBuyerListBuyer;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        return Inertia::render('admin-pages/dashboard/Dashboard');
    }

    public function getStats($lastDays = 30)
    {
        $now = Carbon::now();
        $range = $now->copy()->subDays($lastDays);
        $prev_range_end = $range->copy();
        $prev_range_start = $range->copy()->subDays($lastDays);

        // Ensure $prev_range_start and $prev_range_end are defined before use
        $stats = [
            'from' => $range->format('Y-m-d'),
            'to' => $now->format('Y-m-d'),
            'recent_property_searches' => PropertyFilterHistory::where('created_at', '>=', $range)->count(),
            'recent_property_searches_prev' => PropertyFilterHistory::whereBetween('created_at', [$prev_range_start, $prev_range_end])->count(),
            'recent_property_views' => PropertyViewHistory::where('created_at', '>=', $range)->count(),
            'recent_property_views_prev' => PropertyViewHistory::whereBetween('created_at', [$prev_range_start, $prev_range_end])->count(),
            'recent_question' => AskQuestion::where('created_at', '>=', $range)->count(),
            'recent_question_prev' => AskQuestion::whereBetween('created_at', [$prev_range_start, $prev_range_end])->count(),
            'recent_users' => User::where('created_at', '>=', $range)->count(),
            'recent_users_prev' => User::whereBetween('created_at', [$prev_range_start, $prev_range_end])->count(),
            'recent_email_campaigns' => Campaign::where('created_at', '>=', $range)->count(),
            'recent_email_campaigns_prev' => Campaign::whereBetween('created_at', [$prev_range_start, $prev_range_end])->count(),
        ];
        return $stats;
    }

    /**
     * Display all users
     */
    public function users(Request $request)
    {
        $query = User::withCount(['propertyViewHistories']);


        $propertyTypes = PropertyHelper::getComboboxPropertyTypes();
        $invStgy = PropertyHelper::getComboboxInvestStrategies();
        $counties = LocationHelper::getComboboxCounties();

        // Search by name, email, or phone
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone_number', 'like', "%$search%");
            });
        }

        // Filter by user type
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->input('user_type'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = 'desc';
        if ($sortBy === 'name') {
            $query->orderBy('name', 'asc');
        } elseif ($sortBy === 'property_view_histories_count') {
            $query->orderBy('property_view_histories_count', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(perPage: 10)->withQueryString();



        return Inertia::render('admin-pages/users/Users', [
            'users' => $users,
            'propertyTypes' => $propertyTypes,
            'invStgy' => $invStgy,
            'counties' => $counties,
            'emailCategories' => config('user_email_config.categories_description'),
        ]);
    }

    /**
     * Display all property searches
     */
    public function propertySearches()
    {
        $searches = PropertyViewHistory::with('user')
            ->latest()
            ->paginate(20);

        return Inertia::render('admin/PropertySearches', [
            'searches' => $searches,
        ]);
    }

    /**
     * Display all questions
     */
    public function questions()
    {
        $questions = AskQuestion::with('user')
            ->latest()
            ->paginate(20);

        return Inertia::render('admin/Questions', [
            'questions' => $questions,
        ]);
    }

    /**
     * Show user details
     */
    public function showUser($id)
    {
        $user = User::with([
            'propertyViewHistories',
            'unlistedViewHistories',
            'savedSearches',
            'propertyFilterHistories',
            'leadSources',
            // 'buyBoxRelation', // Removed invalid relationship
        ])->findOrFail($id);

        // For compatibility: add buy_box attribute for frontend
        $user->buy_box = $user->buyBox;

        // Prepare summary and recent data
        $summary = [
            'views' => $user->total_views,
            'unlisted_views' => $user->unlisted_views,
            'searches' => $user->searches_count,
            'saved_searches' => $user->saved_searches_count,
        ];
        $recentViews = $user->recent_views;
        $recentSavedSearches = $user->recent_saved_searches;
        $leadSource = $user->getLatestLeadSource();
        return response()->json([
            'user' => $user,
            'summary' => $summary,
            'recentViews' => $recentViews,
            'recentSavedSearches' => $recentSavedSearches,
            'leadSource' => $leadSource,
        ]);
    }

    /**
     * Update user details
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            // 'email' => 'required|email|unique:users,email,' . $id,
            'user_type' => 'required|in:1,2,3',
            'phone_number' => 'nullable|string|max:20',
            'street_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip' => 'nullable|string|max:10',
            'enable_mls_offer' => 'boolean',
            'subscribed_counties' => 'nullable|array',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'last_name' => $request->last_name,
            // 'email' => $request->email,
            'user_type' => $request->user_type,
            'phone_number' => $request->phone_number,
            'street_address' => $request->street_address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'enable_mls_offer' => $request->enable_mls_offer,
            'subscribed_counties' => $request->subscribed_counties ?? [],
        ]);

        return back()->with('success', 'User updated successfully');
    }

    /**
     * Update user buy box
     */
    public function updateUserBuyBox(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'investment_strategy' => 'nullable|array',
            'counties_invest' => 'nullable|array',
            'property_types' => 'nullable|array',
            'arv_min' => 'nullable|numeric|min:0',
            'arv_max' => 'nullable|numeric|min:0',
            'bath_min' => 'nullable|numeric|min:0',
            'bath_max' => 'nullable|numeric|min:0',
            'bed_min' => 'nullable|numeric|min:0',
            'bed_max' => 'nullable|numeric|min:0',
            'cashflow_min' => 'nullable|numeric',
            'cashflow_max' => 'nullable|numeric',
            'delta_psf_min' => 'nullable|numeric',
            'delta_psf_max' => 'nullable|numeric',
            'flip_profit_min' => 'nullable|numeric|min:0',
            'flip_profit_max' => 'nullable|numeric|min:0',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'sqft_min' => 'nullable|numeric|min:0',
            'sqft_max' => 'nullable|numeric|min:0',
            'year_build_min' => 'nullable|numeric|min:1800',
            'year_build_max' => 'nullable|numeric|min:1800',
        ]);

        $buyBox = $user->buyBox;

        if (!$buyBox) {
            $buyBox = new \App\Models\BuyBox();
            $buyBox->user_id = $user->id;
        }

        $buyBox->fill($request->only([
            'investment_strategy',
            'counties_invest',
            'property_types',
            'arv_min',
            'arv_max',
            'bath_min',
            'bath_max',
            'bed_min',
            'bed_max',
            'cashflow_min',
            'cashflow_max',
            'delta_psf_min',
            'delta_psf_max',
            'flip_profit_min',
            'flip_profit_max',
            'price_min',
            'price_max',
            'sqft_min',
            'sqft_max',
            'year_build_min',
            'year_build_max',
        ]));

        $buyBox->save();

        return back()->with('success', 'Buy Box updated successfully');
    }

    /**
     * Update user email preferences
     */
    public function updateUserEmail(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'emailVerified' => 'boolean',
            'global' => 'boolean',
            'emailUnsubscribedListPreference' => 'nullable|array',
        ]);

        $user->update([
            'email_unsubscribed_global' => $request->input('global'),
            'email_unsubscribed_list_preference' => $request->input('emailUnsubscribedListPreference'),
            'email_verified_at' => $request->input('emailVerified') ? $user->email_verified_at ?? now()->toDateTimeString() : null,
            'email_verified' => $request->input('emailVerified') ? true : false,
        ]);
        return back()->with('success', 'Email preferences updated successfully');
    }

    /**
     * Update user business information
     */
    public function updateUserBusiness(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'companyName' => 'nullable|string|max:255',
            'companyEmail' => 'nullable|email|max:255',
            'brokerageName' => 'nullable|string|max:255',
            'agentLicenseNumber' => 'nullable|string|max:255',
        ]);

        $user->update([
            'company_name' => $request->companyName,
            'company_email' => $request->companyEmail,
            'brokerage_name' => $request->brokerageName,
            'agent_license_number' => $request->agentLicenseNumber,
        ]);

        return back()->with('success', 'Business information updated successfully');
    }


    public function companyLogoUpload(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'image' => 'required|image|max:15000',
        ]);

        if ($request->hasFile('image')) {
            try {
                $imageManager = new ImageManager(new Driver());
                $image = $imageManager->read($request->file('image')->getPathname());
                $resizedImage = $image->resizeDown(512, 512, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->toJpeg(90);

                $path = 'company-logo/' . $id . '/' . uniqid() . '.jpg';

                Storage::disk('s3')->put($path, (string) $resizedImage, 'public');

                $url = Storage::disk('s3')->url($path);
                AwsS3History::addHistory('company-logo', $path, $url, $id);
                if ($url) {
                    User::where('id', $id)->update([
                        'company_logo' => $url,
                    ]);

                    return response()->json(['success' => true, 'message' => 'Your company logo has been uploaded!', 'url' => $url]);
                }
            } catch (\Exception $e) {
                AwsS3History::addHistory('company-logo', null, null, $id, $e->getMessage());
            }

            return response()->json(['success' => false, 'error' => 'Image could not be uploaded, Please try again!']);
        }
        return response()->json(['error' => 'File could not be uploaded'], 400);
    }

    /**
     * Upload user profile picture
     */
    public function profilePictureUpload(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'image' => 'required|image|max:15000',
        ]);

        if ($request->hasFile('image')) {
            try {
                $imageManager = new ImageManager(new Driver());
                $image = $imageManager->read($request->file('image')->getPathname());
                $resizedImage = $image->resizeDown(512, 512, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->toJpeg(90);

                $path = 'profile-picture/' . $user->uuid . '/' . uniqid() . '.jpg';

                Storage::disk('s3')->put($path, (string) $resizedImage, 'public');

                $url = Storage::disk('s3')->url($path);
                AwsS3History::addHistory('profile-picture', $path, $url, $id);
                if ($url) {
                    User::where('id', $id)->update([
                        'profile_picture' => $url,
                    ]);

                    return response()->json(['success' => true, 'message' => 'Your profile picture has been uploaded!', 'url' => $url]);
                }
            } catch (\Exception $e) {
                AwsS3History::addHistory('profile-picture', null, null, $id, $e->getMessage());
            }

            return response()->json(['success' => false, 'error' => 'Image could not be uploaded, Please try again!']);
        }
        return response()->json(['error' => 'File could not be uploaded'], 400);
    }

    /**
     * Delete user profile picture
     */
    public function deleteProfilePicture($id)
    {
        $user = User::findOrFail($id);

        if ($user->profile_picture) {
            // Extract the path from the URL
            $url = $user->profile_picture;
            $path = str_replace(Storage::disk('s3')->url(''), '', $url);

            // Delete from S3
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }

            // Update user record
            $user->update(['profile_picture' => null]);

            return response()->json(['success' => true, 'message' => 'Profile picture deleted successfully']);
        }

        return response()->json(['error' => 'No profile picture found'], 404);
    }

    /**
     * Update user status
     */
    public function updateUserStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $user->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'User status updated successfully');
    }

    /**
     * Login as user (admin impersonation)
     */
    public function loginAsUser($id)
    {
        $targetUser = User::findOrFail($id);
        $adminUser = auth()->user();

        // Ensure the current user is an admin
        if (!$adminUser->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prevent admin from impersonating another admin
        if ($targetUser->isAdmin()) {
            return response()->json(['error' => 'Cannot impersonate another admin user'], 403);
        }

        // Store the original admin user ID in session for later restoration
        session(['impersonating_admin_id' => $adminUser->id]);

        // Login as the target user
        auth()->login($targetUser);

        return response()->json([
            'success' => true,
            'message' => "Now logged in as {$targetUser->name}",
            'redirect_url' => route('dashboard')
        ]);
    }

    /**
     * Check if user is currently being impersonated
     */
    public function checkImpersonation()
    {
        $isImpersonating = session()->has('impersonating_admin_id');
        $adminId = session('impersonating_admin_id');

        return response()->json([
            'is_impersonating' => $isImpersonating,
            'admin_id' => $adminId,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Stop impersonation and return to admin account
     */
    public function stopImpersonation()
    {
        $adminId = session('impersonating_admin_id');

        if (!$adminId) {
            // If no impersonation session, just redirect to dashboard
            return response()->json([
                'success' => true,
                'message' => "No impersonation session found",
                'redirect_url' => route('dashboard')
            ]);
        }

        $adminUser = User::find($adminId);

        if (!$adminUser || !$adminUser->isAdmin()) {
            // Clear invalid session and redirect to dashboard
            session()->forget('impersonating_admin_id');
            return response()->json([
                'success' => true,
                'message' => "Invalid admin session cleared",
                'redirect_url' => route('dashboard')
            ]);
        }

        // Clear the impersonation session
        session()->forget('impersonating_admin_id');

        // Login back as the admin
        auth()->login($adminUser);

        return response()->json([
            'success' => true,
            'message' => "Returned to admin account",
            'redirect_url' => route('admin.index')
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Check if user is not an admin
        if ($user->is_admin) {
            return back()->with('error', 'Cannot delete admin users');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully');
    }

    /**
     * Display all property information
     */
    public function propertyInformation()
    {
        return Inertia::render('admin-pages/property-information/PropertyInformation');
    }


    /**
     * Display all upgrade features
     */
    public function upgradeFeatures(Request $request)
    {
        $query = UpgradeFeature::query();

        // Search by feature or description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('feature', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        // Filter by user type
        if ($request->filled('user_type')) {
            $query->where('group', $request->input('user_type'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'order');
        if ($sortBy === 'feature') {
            $query->orderBy('feature', 'asc');
        } elseif ($sortBy === 'created_at') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sortBy === 'created_at_desc') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('order', 'asc');
        }

        $upgradeFeatures = $query->paginate(perPage: 10)->withQueryString();

        return Inertia::render('admin-pages/upgrade-features/UpgradeFeatures', [
            'upgradeFeatures' => $upgradeFeatures,
        ]);
    }

    /**
     * Show upgrade feature details
     */
    public function showUpgradeFeature($id)
    {
        $feature = UpgradeFeature::findOrFail($id);

        return response()->json([
            'feature' => $feature,
        ]);
    }

    /**
     * Store new upgrade feature
     */
    public function storeUpgradeFeature(Request $request)
    {
        $request->validate([
            'feature' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'group' => 'required|in:1,2,3',
            'order' => 'required|integer|min:1',
            'no_access' => 'boolean',
        ]);

        UpgradeFeature::create([
            'feature' => $request->feature,
            'description' => $request->description,
            'group' => $request->group,
            'order' => $request->order,
            'no_access' => $request->boolean('no_access'),
        ]);

        return back()->with('success', 'Upgrade feature created successfully');
    }

    /**
     * Update upgrade feature
     */
    public function updateUpgradeFeature(Request $request, $id)
    {
        $feature = UpgradeFeature::findOrFail($id);

        $request->validate([
            'feature' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'group' => 'required|in:1,2,3',
            'order' => 'required|integer|min:1',
            'no_access' => 'boolean',
        ]);

        $feature->update([
            'feature' => $request->feature,
            'description' => $request->description,
            'group' => $request->group,
            'order' => $request->order,
            'no_access' => $request->boolean('no_access'),
        ]);

        return back()->with('success', 'Upgrade feature updated successfully');
    }

    /**
     * Delete upgrade feature
     */
    public function destroyUpgradeFeature($id)
    {
        $feature = UpgradeFeature::findOrFail($id);
        $feature->delete();

        return back()->with('success', 'Upgrade feature deleted successfully');
    }

    /**
     * Display all county states
     */
    public function countyStates(Request $request)
    {
        $query = CountyState::query();

        // Search by county, display, or state
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('county', 'like', "%$search%")
                    ->orWhere('display', 'like', "%$search%")
                    ->orWhere('state', 'like', "%$search%");
            });
        }

        // Filter by state
        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'display');
        if ($sortBy === 'county') {
            $query->orderBy('county', 'asc');
        } elseif ($sortBy === 'state') {
            $query->orderBy('state', 'asc');
        } elseif ($sortBy === 'created_at') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('display', 'asc');
        }

        $countyStates = $query->paginate(perPage: 10)->withQueryString();

        return Inertia::render('admin-pages/county-states/CountyStates', [
            'countyStates' => $countyStates,
        ]);
    }

    /**
     * Show county state details
     */
    public function showCountyState($id)
    {
        $countyState = CountyState::findOrFail($id);

        return response()->json([
            'countyState' => $countyState,
        ]);
    }

    /**
     * Store new county state
     */
    public function storeCountyState(Request $request)
    {
        $request->validate([
            'county' => 'required|string|max:255',
            'display' => 'required|string|max:255',
            'fips' => 'nullable|string|max:50',
            'state' => 'required|string|max:2',
            'slug' => 'required|string|max:255|unique:county_states,slug',
        ]);

        CountyState::create([
            'county' => $request->county,
            'display' => $request->display,
            'fips' => $request->fips,
            'state' => $request->state,
            'slug' => $request->slug,
        ]);

        return back()->with('success', 'County state created successfully');
    }

    /**
     * Update county state
     */
    public function updateCountyState(Request $request, $id)
    {
        $countyState = CountyState::findOrFail($id);

        $request->validate([
            'county' => 'required|string|max:255',
            'display' => 'required|string|max:255',
            'fips' => 'nullable|string|max:50',
            'state' => 'required|string|max:2',
            'slug' => 'required|string|max:255|unique:county_states,slug,' . $id,
        ]);

        $countyState->update([
            'county' => $request->county,
            'display' => $request->display,
            'fips' => $request->fips,
            'state' => $request->state,
            'slug' => $request->slug,
        ]);

        return back()->with('success', 'County state updated successfully');
    }

    /**
     * Delete county state
     */
    public function destroyCountyState($id)
    {
        $countyState = CountyState::findOrFail($id);
        $countyState->delete();

        return back()->with('success', 'County state deleted successfully');
    }

    /**
     * Display all testimonials
     */
    public function testimonials(Request $request)
    {
        $query = \App\Models\Testimonial::query();

        // Search by name, email, or description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $rating = $request->input('rating');
            $query->where('rate', '>=', $rating);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'published_date');
        if ($sortBy === 'published_date_desc') {
            $query->orderBy('published_date', 'desc');
        } elseif ($sortBy === 'rate') {
            $query->orderBy('rate', 'desc');
        } elseif ($sortBy === 'name') {
            $query->orderBy('name', 'asc');
        } elseif ($sortBy === 'created_at') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sortBy === 'created_at_desc') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('published_date', 'asc');
        }

        $testimonials = $query->paginate(20);

        return Inertia::render('admin-pages/testimonials/Testimonials', [
            'testimonials' => $testimonials
        ]);
    }

    /**
     * Show a specific testimonial
     */
    public function showTestimonial($id)
    {
        $testimonial = \App\Models\Testimonial::findOrFail($id);
        return response()->json(['testimonial' => $testimonial]);
    }

    /**
     * Store a new testimonial
     */
    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:2000',
            'email' => 'nullable|email|max:255',
            'name' => 'nullable|string|max:255',
            'profile_image' => 'nullable|string',
            'published_date' => 'nullable|date',
            'rate' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
        ]);

        $testimonial = \App\Models\Testimonial::create($validated);

        return redirect()->back()->with('success', 'Testimonial created successfully');
    }

    /**
     * Update a testimonial
     */
    public function updateTestimonial(Request $request, $id)
    {
        $testimonial = \App\Models\Testimonial::findOrFail($id);

        $validated = $request->validate([
            'description' => 'required|string|max:2000',
            'email' => 'nullable|email|max:255',
            'name' => 'nullable|string|max:255',
            'profile_image' => 'nullable|string',
            'published_date' => 'nullable|date',
            'rate' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
        ]);

        $testimonial->update($validated);

        return redirect()->back()->with('success', 'Testimonial updated successfully');
    }

    /**
     * Delete a testimonial
     */
    public function destroyTestimonial($id)
    {
        $testimonial = \App\Models\Testimonial::findOrFail($id);
        $testimonial->delete();

        return redirect()->back()->with('success', 'Testimonial deleted successfully');
    }

    /**
     * Display all request a showings
     */
    public function showing(Request $request)
    {
        $query = RequestAShowing::with(['user', 'wholesale']);

        // Search by address, city, or user
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_street_address', 'like', "%$search%")
                    ->orWhere('city', 'like', "%$search%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
            });
        }

        // Filter by state
        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        if ($sortBy === 'preferred_time') {
            $query->orderBy('preferred_time', 'asc');
        } elseif ($sortBy === 'full_street_address') {
            $query->orderBy('full_street_address', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $requestAShowings = $query->paginate(perPage: 10)->withQueryString();

        return Inertia::render('admin-pages/requests/RequestAShowing', [
            'requestAShowings' => $requestAShowings,
        ]);
    }

    /**
     * Display all ask questions
     */
    public function askQuestions(Request $request)
    {
        $query = AskQuestion::with(['user', 'wholesale']);

        // Search by name, email, address, or question
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('full_street_address', 'like', "%$search%")
                    ->orWhere('question', 'like', "%$search%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
            });
        }

        // Filter by state
        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        if ($sortBy === 'name') {
            $query->orderBy('name', 'asc');
        } elseif ($sortBy === 'email') {
            $query->orderBy('email', 'asc');
        } elseif ($sortBy === 'full_street_address') {
            $query->orderBy('full_street_address', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $askQuestions = $query->paginate(perPage: 10)->withQueryString();

        return Inertia::render('admin-pages/requests/AskQuestions', [
            'askQuestions' => $askQuestions,
        ]);
    }

    /**
     * Display all contact us requests
     */
    public function contactUsRequests(Request $request)
    {
        $query = ContactUsRequest::query();

        // Search by name, email, or message
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('message', 'like', "%$search%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        if ($sortBy === 'name') {
            $query->orderBy('first_name', 'asc');
        } elseif ($sortBy === 'email') {
            $query->orderBy('email', 'asc');
        } elseif ($sortBy === 'status') {
            $query->orderBy('status', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $contactUsRequests = $query->paginate(perPage: 10)->withQueryString();

        return Inertia::render('admin-pages/requests/ContactUsRequests', [
            'contactUsRequests' => $contactUsRequests,
        ]);
    }

    /**
     * Display all instant offers
     */
    public function instantOffers(Request $request)
    {
        $query = InstantOffer::with(['user', 'wholesale']);

        // Search by name, email, address, or buyer name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('full_street_address', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('buyer_name_llc', 'like', "%$search%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
            });
        }

        // Filter by state
        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        if ($sortBy === 'offer_price') {
            $query->orderBy('offer_price', 'desc');
        } elseif ($sortBy === 'name') {
            $query->orderBy('name', 'asc');
        } elseif ($sortBy === 'full_street_address') {
            $query->orderBy('full_street_address', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $instantOffers = $query->paginate(perPage: 10)->withQueryString();

        return Inertia::render('admin-pages/requests/InstantOffers', [
            'instantOffers' => $instantOffers,
        ]);
    }

    /**
     * Send email reply to request
     */
    public function sendEmail(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_email' => 'required|email',
            'recipient_name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'request_type' => 'required|in:showing,question,offer,contact',
        ]);

        try {
            $emailService = app(EmailService::class);

            // Get email template configuration based on request type
            $templateConfig = $this->getEmailTemplateConfig($request->request_type);

            $success = $emailService->sendCustom(
                $request->recipient_email,
                $request->recipient_name,
                $templateConfig['category'],
                $templateConfig['template_id'],
                [
                    'subject' => $request->subject,
                    'body' => $request->content,
                ]
            );

            if ($success) {
                // Update status for contact us requests
                if ($request->request_type === 'contact') {
                    ContactUsRequest::where('email', $request->recipient_email)
                        ->update([
                            'status' => 'replied',
                            'replied_at' => now(),
                        ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Email sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send email'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Email send error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the email'
            ], 500);
        }
    }

    /**
     * Get email template configuration for request types
     */
    private function getEmailTemplateConfig(string $requestType): array
    {


        $configs = [
            'showing' => [
                'category' => 'admin_reply_showing',
                'template_id' => config('mail.templates.admin_reply_showing'),
            ],
            'question' => [
                'category' => 'admin_reply_question',
                'template_id' => config('mail.templates.admin_reply_question'),
            ],
            'offer' => [
                'category' => 'admin_reply_offer',
                'template_id' => config('mail.templates.admin_reply_offer'),
            ],
            'contact' => [
                'category' => 'admin_reply_contact',
                'template_id' => config('mail.templates.admin_reply_contact'),
            ],
        ];

        return $configs[$requestType] ?? $configs['question'];
    }


    public function supportTickets(Request $request)
    {
        return app(SupportTicketController::class)->index($request);
    }

    /**
     * Get HTTP errors for monitoring
     */
    public function getHttpErrors(Request $request)
    {
        $hours = $request->get('hours', 24);
        $type = $request->get('type', 'all');

        try {
            // Use the artisan command to get errors
            $output = \Artisan::call('monitor:http-errors', [
                '--hours' => $hours,
                '--type' => $type
            ]);

            // Parse the output to extract errors
            $logPath = storage_path('logs/laravel.log');
            $errors = [];

            if (File::exists($logPath)) {
                $logContent = File::get($logPath);
                $lines = explode("\n", $logContent);

                $cutoffTime = now()->subHours($hours);

                foreach ($lines as $line) {
                    if (empty($line))
                        continue;

                    // Parse log timestamp
                    if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                        $logTime = \Carbon\Carbon::parse($matches[1]);

                        if ($logTime->lt($cutoffTime)) {
                            continue;
                        }

                        // Check for HTTP-related errors
                        if (str_contains($line, 'ERROR') && $this->isHttpError($line, $type)) {
                            $errors[] = [
                                'id' => uniqid(),
                                'time' => $logTime->toISOString(),
                                'type' => $this->determineErrorType($line),
                                'message' => $this->extractErrorMessage($line),
                                'details' => $this->extractErrorDetails($line)
                            ];
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'errors' => $errors,
                'total' => count($errors)
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to get HTTP errors: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve HTTP errors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function isHttpError($line, $type)
    {
        $httpKeywords = [
            'DB API',
            'HTTP',
            'request failed',
            'Network error',
            'timeout',
            'connection',
            'curl',
            'GuzzleHttp',
            'ClientException'
        ];

        if ($type === 'all') {
            return collect($httpKeywords)->contains(function ($keyword) use ($line) {
                return stripos($line, $keyword) !== false;
            });
        }

        if ($type === 'api') {
            return stripos($line, 'DB API') !== false;
        }

        if ($type === 'network') {
            return stripos($line, 'Network error') !== false || stripos($line, 'connection') !== false;
        }

        if ($type === 'timeout') {
            return stripos($line, 'timeout') !== false;
        }

        return false;
    }

    private function extractErrorMessage($line)
    {
        if (preg_match('/\] local\.ERROR: (.+)$/', $line, $matches)) {
            return $matches[1];
        }

        return substr($line, 0, 200) . (strlen($line) > 200 ? '...' : '');
    }

    private function determineErrorType($line)
    {
        if (stripos($line, 'DB API') !== false) {
            return 'API Request';
        }

        if (stripos($line, 'Network error') !== false) {
            return 'Network';
        }

        if (stripos($line, 'timeout') !== false) {
            return 'Timeout';
        }

        if (stripos($line, 'connection') !== false) {
            return 'Connection';
        }

        return 'HTTP';
    }

    private function extractErrorDetails($line)
    {
        $details = [];

        // Try to extract JSON details from the log line
        if (preg_match('/\{.*\}/', $line, $matches)) {
            try {
                $jsonData = json_decode($matches[0], true);
                if ($jsonData) {
                    $details = $jsonData;
                }
            } catch (\Exception $e) {
                // Ignore JSON parsing errors
            }
        }

        return $details;
    }

    /**
     * Display lead sources analytics
     */
    public function leadSources(Request $request)
    {
        $query = LeadSource::with('user');

        // Search by page name or user email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('page_name', 'like', "%$search%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('email', 'like', "%$search%")
                            ->orWhere('name', 'like', "%$search%");
                    });
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('visited_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('visited_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        // Get lead sources with pagination
        $leadSources = $query->orderBy('visited_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Get summary statistics
        $totalLeadSources = LeadSource::count();
        $uniquePages = LeadSource::distinct('page_name')->count();
        $totalUsers = LeadSource::distinct('user_id')->count();

        // Get top lead sources by page
        $topLeadSources = LeadSource::selectRaw('page_name, COUNT(*) as count')
            ->groupBy('page_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return Inertia::render('admin-pages/LeadSources', [
            'leadSources' => $leadSources,
            'stats' => [
                'total' => $totalLeadSources,
                'uniquePages' => $uniquePages,
                'totalUsers' => $totalUsers,
            ],
            'topLeadSources' => $topLeadSources,
        ]);
    }
    /**
     * Admin Skip Trace Statistics Dashboard
     */
    public function skipTraceStats(Request $request)
    {
        $lastDays = (int) $request->get('lastDays', 30);
        $from = now()->subDays($lastDays);
    
        $users = User::query()->get()->map(function ($user) use ($from) {
    
            // Only buyers where THIS user actually ran the API
            $rows = MyBuyerListBuyer::query()
                ->with('skipTraceResult')                      // to count phones/emails
                ->whereHas('list', fn ($q) => $q->where('user_id', $user->id))
                ->where('last_trace_source', 'api')            // exclude community
                ->where('last_traced_by_user_id', $user->id)   // only their own API runs
                ->whereNotNull('last_traced_at')
                ->where('last_traced_at', '>=', $from)
                ->get();
    
            $totalApiRuns    = $rows->count();
            $successfulCount = $rows->where('last_trace_success', true)->count();
            $failedCount     = $totalApiRuns - $successfulCount;
    
            $totalEmailsReturned = $rows->sum(function ($r) {
                $sr = $r->skipTraceResult;
                return is_array($sr?->emails) ? count($sr->emails) : 0;
            });
            $totalPhonesReturned = $rows->sum(function ($r) {
                $sr = $r->skipTraceResult;
                return is_array($sr?->phone_numbers) ? count($sr->phone_numbers) : 0;
            });
    
            // BILLING: only successful API runs by this user
            $cost = $successfulCount * 0.10;
    
            return [
                'id'                    => $user->id,
                'name'                  => $user->name,
                'email'                 => $user->email,
                'user_type'             => $user->user_type,
                'total_skip_traces'     => $totalApiRuns,      // attempts this user made
                'successful_count'      => $successfulCount,   // successes from their API runs
                'failed_count'          => $failedCount,
                'total_emails_returned' => $totalEmailsReturned,
                'total_phones_returned' => $totalPhonesReturned,
                'success_rate'          => $totalApiRuns > 0 ? round(($successfulCount / $totalApiRuns) * 100, 2) : 0,
                'cost'                  => round($cost, 2),
            ];
        });
    
        $overallStats = [
            'total_users'            => $users->count(),
            'total_skip_traces'      => $users->sum('total_skip_traces'),
            'total_successful'       => $users->sum('successful_count'),
            'total_failed'           => $users->sum('failed_count'),
            'total_emails_returned'  => $users->sum('total_emails_returned'),
            'total_phones_returned'  => $users->sum('total_phones_returned'),
            'overall_success_rate'   => $users->sum('total_skip_traces') > 0
                                        ? round(($users->sum('successful_count') / $users->sum('total_skip_traces')) * 100, 2)
                                        : 0,
            'total_cost'             => round($users->sum('cost'), 2),
        ];
    
        return Inertia::render('admin-pages/skip-trace/SkipTraceStats', [
            'users'        => $users,
            'overallStats' => $overallStats,
            'lastDays'     => $lastDays,
        ]);
    }
    /**
     * Individual User Skip Trace Details
     */
    public function skipTraceUserDetail(Request $request, $userId)
    {
        $lastDays = (int) $request->get('lastDays', 30);
        $from = now()->subDays($lastDays);
    
        $user = User::findOrFail($userId);
    
        // Load lists and filter buyers to only THIS user's API runs
        $lists = MyBuyerList::query()
        ->where('user_id', $user->id)
        ->with(['buyers' => function ($q) use ($user, $from) {
            $q->with('skipTraceResult')
              ->where('last_trace_source', 'api')
              ->where('last_traced_by_user_id', $user->id)
              ->whereNotNull('last_traced_at')
              ->where('last_traced_at', '>=', $from) // This is already correct
              ->orderBy('last_traced_at', 'desc'); // Add ordering
        }])
        ->get();
    
        $buyerLists = $lists->map(function ($list) {
            $buyers = $list->buyers;
    
            $totalApiRuns    = $buyers->count();
            $successfulCount = $buyers->where('last_trace_success', true)->count();
            $failedCount     = $totalApiRuns - $successfulCount;
    
            $totalEmailsReturned = $buyers->sum(function ($b) {
                $r = $b->skipTraceResult;
                return is_array($r?->emails) ? count($r->emails) : 0;
            });
            $totalPhonesReturned = $buyers->sum(function ($b) {
                $r = $b->skipTraceResult;
                return is_array($r?->phone_numbers) ? count($r->phone_numbers) : 0;
            });
    
            return [
                'id'                    => $list->id,
                'name'                  => $list->name,
                'buyer_count'           => $totalApiRuns,
                'skip_traced_count'     => $totalApiRuns,
                'successful_count'      => $successfulCount,
                'failed_count'          => $failedCount,
                'total_emails_returned' => $totalEmailsReturned,
                'total_phones_returned' => $totalPhonesReturned,
                'created_at'            => $list->created_at->format('M j, Y'),
                'buyers' => $buyers->map(function ($b) {
                    $r = $b->skipTraceResult;
                    return [
                        'id'                  => $b->id,
                        'investor_identifier' => $b->investor_identifier,
                        'mrp_fullstreet'      => $b->mrp_fullstreet,
                        'mrp_city'            => $b->mrp_city,
                        'mrp_state'           => $b->mrp_state,
                        'mrp_zip'             => $b->mrp_zip,
                        'mrp_sales_price'     => $b->mrp_sales_price,
                        'mrp_purchase'        => $b->mrp_purchase,
                        'first_name'          => $r->first_name ?? null,
                        'last_name'           => $r->last_name ?? null,
                        'phone_numbers'       => $r->phone_numbers ?? [],
                        'emails'              => $r->emails ?? [],
                        'skip_traced'         => (bool) $b->last_trace_success, // success of this user’s API run
                    ];
                }),
            ];
        });
    
        return Inertia::render('admin-pages/skip-trace/SkipTraceUserDetail', [
            'user'       => $user,
            'buyerLists' => $buyerLists,
            'lastDays'   => $lastDays,
        ]);
    }
}