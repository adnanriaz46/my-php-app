<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyEmailVerificationController extends Controller
{
    public function __construct(
        protected EmailService $emailService
    ) {}

    /**
     * Mark the company email as verified.
     */
    public function verify(Request $request): RedirectResponse
    {
        $user = User::findOrFail($request->route('id'));

        if ($request->route('hash') !== sha1($user->company_email)) {
            return redirect()->route('dashboard')->with('error', 'Invalid verification link.');
        }

        if ($user->hasVerifiedCompanyEmail()) {
            return redirect()->route('dashboard')->with('success', 'Company email already verified.');
        }

        if ($user->markCompanyEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('dashboard')->with('success', 'Company email verified successfully!');
    }

    /**
     * Send a new company email verification notification.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->company_email) {
            return back()->with('error', 'No company email address found.');
        }

        if ($user->hasVerifiedCompanyEmail()) {
            return back()->with('success', 'Company email already verified.');
        }

        $this->sendCompanyEmailVerificationEmail($user);

        return back()->with('status', 'company-email-verification-sent')
                    ->with('success', 'Company email verification link sent!');
    }

    /**
     * Show the company email verification prompt.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        if (!$user->company_email) {
            return redirect()->route('dashboard')->with('error', 'No company email address found.');
        }

        if ($user->hasVerifiedCompanyEmail()) {
            return redirect()->route('dashboard')->with('success', 'Company email already verified.');
        }

        return view('auth.verify-company-email', [
            'user' => $user,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Send company email verification email using EmailService
     */
    private function sendCompanyEmailVerificationEmail(User $user): bool
    {
        $verificationUrl = $user->getCompanyEmailVerificationUrl();
        
        $data = [
            'user_name' => $user->first_name,
            'verification_url' => $verificationUrl,
            'company_email' => $user->company_email,
            'expires_in' => '60 minutes'
        ];

        return $this->emailService->sendCustom(
            $user->company_email,
            $user->name,
            'company_email_verification',
            'd-174de7dd1c87417e9bf9ec77c61bff5c', // Use existing template ID
            $data
        );
    }
}
