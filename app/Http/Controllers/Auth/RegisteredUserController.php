<?php

namespace App\Http\Controllers\Auth;

use App\Helper\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LeadSource;
use App\Services\EmailService;
use Carbon\Carbon;
use Cookie;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function __construct(
        protected EmailService $emailService
    ) {}
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'phone' => 'required|string|max:15|regex:/^(\+1\s?)?(\(?\d{3}\)?[\s.-]?)\d{3}[\s.-]?\d{4}$/',
                'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ],
            [
                'phone.regex' => 'The phone number must be a valid US number (e.g., (123) 456-7890).',
            ]
        );

        $userData = [
            'uuid' => (string) Str::uuid(),
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ];

        if (Cookie::has('referral_user') && Cookie::get('referral_user') != null) {
            $referralUser = User::where('uuid', Cookie::get('referral_user'))->first();
            if ($referralUser) {
                $userData['affiliated_user'] = $referralUser->id;
                $userData['affiliate_eligible'] = true;
                $userData['affiliate_slug'] = (string) Str::uuid();
            }
        }

        $user = User::create($userData);

        // Store lead source data if available
        $this->storeLeadSource($user, $request);

        event(new Registered($user));

        // Send email verification directly
        $this->sendEmailVerificationEmail($user);

        Auth::login($user);

        return to_route('dashboard');
    }


    public function unsubscriptionPage($userToken)
    {
        $user = User::where('uuid', $userToken)->firstOrFail();
        return Inertia::render('auth/Unsubscription', [
            'emailCategories' => config('user_email_config.categories_description'),
            'user' => $user,
        ]);
    }

    public function unsubscriptionSubmit(Request $request)
    {

        $request->validate([
            'user_id' => 'required',
            'global' => "nullable|boolean",
            'emailUnsubscribedListPreference' => "nullable|array",
            'emailUnsubscribedListPreference.*' => 'nullable|string'
        ]);

        $user = User::where('uuid', $request->user_id)->firstOrFail();

        $user->update([
            'email_unsubscribed_global' => $request->input('global'),
            'email_unsubscribed_list_preference' => $request->input('emailUnsubscribedListPreference'),
        ]);

        return to_route('unsubscribe.page', $user->uuid);
    }

    /**
     * Send email verification email using EmailService
     */
    private function sendEmailVerificationEmail($user): bool
    {
        $verificationUrl = $user->getEmailVerificationUrl();
        
        $data = [
            'name' => $user->name,
            'verification_url' => $verificationUrl,
            'contact_support' => 'mailto:support@revamp365.ai'
        ];

        return $this->emailService->sendCustom(
            $user->email,
            $user->name,
            'email_verification',
            config('user_email_config.account.email_verify.template_id'),
            $data
        );
    }

    /**
     * Store lead source data for the newly registered user
     */
    private function storeLeadSource(User $user, Request $request): void
    {
        // Try to get lead source from session first
        $leadSourceData = $request->session()->get('lead_source');
        
        // If not in session, try to get from cookie
        if (!$leadSourceData) {
            $cookieData = $request->session()->get('lead_source_cookie');
            if ($cookieData) {
                $leadSourceData = json_decode($cookieData, true);
            }
        }

        // If we have lead source data, store it
        if ($leadSourceData && isset($leadSourceData['page_name'])) {
            // Convert ISO timestamp to Carbon instance
            $visitedAt = \Carbon\Carbon::parse($leadSourceData['timestamp']);
            
            LeadSource::create([
                'user_id' => $user->id,
                'page_name' => $leadSourceData['page_name'],
                'page_url' => $leadSourceData['page_url'],
                'user_agent' => $leadSourceData['user_agent'],
                'visited_at' => $visitedAt,
            ]);

            // Clear the session data after storing
            $request->session()->forget(['lead_source', 'lead_source_cookie']);
        }
    }
}
