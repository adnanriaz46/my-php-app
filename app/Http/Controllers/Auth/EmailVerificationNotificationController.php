<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function __construct(
        protected EmailService $emailService
    ) {}

    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $this->sendEmailVerificationEmail($user);

        return back()->with('status', 'verification-link-sent')->with('success', 'Verification link sent');
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
}
