<?php

namespace App\Services;

use App\Models\User;
use SendGrid\Mail\Mail;

class EmailService
{
    public function __construct(
        protected EmailPreferenceService $preferenceService,
        protected \SendGrid $sendGrid
    ) {
    }

    public function send(User $user, string $category, string $templateId, array $dynamicData = [])
    {
        if ($this->preferenceService->isUnsubscribed($user, $category)) {
            return false;
        }

        $email = new Mail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        //        $email->setSubject(''); // not used with dynamic templates
        $email->addTo($user->email, $user->name);
        $email->setTemplateId($templateId);
        $email->addDynamicTemplateDatas($dynamicData);
        $email->addCategory($category); // required for SendGrid category tracking

        try {
            $this->sendGrid->send($email);
            return true;
        } catch (\Exception $e) {
            \Log::error("Email send failed: " . $e->getMessage());
            return false;
        }
    }

    public function sendCustom(string $toEmail, string $toName, string $category, string $templateId, array $dynamicData = [])
    {

        $email = new Mail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        //        $email->setSubject(''); // not used with dynamic templates
        $email->addTo($toEmail, $toName);
        $email->setTemplateId($templateId);
        $email->addDynamicTemplateDatas($dynamicData);
        $email->setReplyTo($dynamicData['reply_to_email'] ?? null, $dynamicData['reply_to_name'] ?? null);
        $email->addCategory($category); // required for SendGrid category tracking

        // Add CC if provided
        if (isset($dynamicData['cc_email']) && !empty($dynamicData['cc_email'])) {
            $email->addCc($dynamicData['cc_email']);
        }

        try {
            $this->sendGrid->send($email);
            return true;
        } catch (\Exception $e) {
            \Log::error("Email send failed: " . $e->getMessage());
            return false;
        }
    }


    public function sendCompanyEmailVerification(string $toEmail, string $toName, $verificationUrl)
    {

        $body = <<<HTML
        <p>Hello {$toName},</p>
        <p>Please verify your company email address by clicking the button below.</p>
        <p>This verification is required to ensure the security of your account and to enable certain features.</p>
        <a href="{$verificationUrl}">Verify Company Email Address</a>
        <p>This verification link will expire in 60 minutes.</p>
        <p>Best regards, {$toName}</p>
        HTML;

        $data = [
            'subject' => 'Verify Your Company Email Address',
            'body' => $body
        ];

        return $this->sendCustom($toEmail, $toName, 'company_email_verification', 'd-174de7dd1c87417e9bf9ec77c61bff5c', $data);
    }

    public function sendEmailVerification(string $toEmail, string $toName, $verificationUrl)
    {

        $data = [
            'name' => $toName,
            'verification_url' => $verificationUrl,
            'contact_support' => 'mailto:support@revamp365.ai'
        ];

        return $this->sendCustom(
            $toEmail,
            $toName,
            'email_verification',
            config('user_email_config.account.email_verify.template_id'),
            $data
        );
    }

    public function sendSystem(string $templateId, array $dynamicData = [])
    {
        $systemEmail = config('mail.system_email');
        $email = new Mail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        //        $email->setSubject(''); // not used with dynamic templates
        $email->addTo($systemEmail, 'Revamp365 Website');
        $email->setTemplateId($templateId);
        $email->addDynamicTemplateDatas($dynamicData);

        try {
            $this->sendGrid->send($email);
            return true;
        } catch (\Exception $e) {
            \Log::error("Email send failed: " . $e->getMessage());
            return false;
        }
    }

    /* -- Send email like
        $config = config('user_email_config.categories_description.saved_list_updates');
        $emailService->send($user, $config['category'], $config['template_id'], $data);
    */

}
