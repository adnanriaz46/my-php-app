<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class CampaignUnsubscriptionController extends Controller
{
    /**
     * Show the unsubscription page with confirmation
     */
    public function show(Request $request, $encryptedEmail)
    {
        try {
            // Decrypt the email address
            $email = Crypt::decryptString($encryptedEmail);
            $campaignId = $request->query('campaign_id');

            // Check if email is already unsubscribed
            $isUnsubscribed = DB::table('campaign_unsubscriptions')
                ->where('email', $email)
                ->where(function ($query) use ($campaignId) {
                    $query->where('is_global', true);
                    if ($campaignId) {
                        $query->orWhere('campaign_id', $campaignId);
                    }
                })
                ->exists();

            return Inertia::render('campaign/Unsubscribe', [
                'email' => $email,
                'encryptedEmail' => $encryptedEmail,
                'campaignId' => $campaignId,
                'isUnsubscribed' => $isUnsubscribed,
                'status' => $request->session()->get('status'),
                'error' => $request->session()->get('error'),
            ]);

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Invalid unsubscription link.');
        }
    }

    /**
     * Process the unsubscription
     */
    public function unsubscribe(Request $request, $encryptedEmail)
    {
        try {
            // Decrypt the email address
            $email = Crypt::decryptString($encryptedEmail);
            $campaignId = $request->input('campaign_id');

            // Validate the request
            $validator = Validator::make($request->all(), [
                'reason' => 'nullable|string|max:500',
                'confirm' => 'required|boolean|accepted',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Check if already unsubscribed
            $existingUnsubscription = DB::table('campaign_unsubscriptions')
                ->where('email', $email)
                ->where(function ($query) use ($campaignId) {
                    $query->where('is_global', true);
                    if ($campaignId) {
                        $query->orWhere('campaign_id', $campaignId);
                    }
                })
                ->first();

            if ($existingUnsubscription) {
                $message = $campaignId
                    ? 'This email address is already unsubscribed from this campaign.'
                    : 'This email address is already unsubscribed from our campaigns.';
                return back()->with('status', $message);
            }

            // Determine if this is a global or campaign-specific unsubscription
            $isGlobal = true;

            // Create the unsubscription record
            DB::table('campaign_unsubscriptions')->insert([
                'email' => $email,
                'campaign_id' => $campaignId,
                'user_id' => null, // Non-registered user
                'reason' => $request->input('reason'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'is_global' => $isGlobal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $message = $isGlobal
                ? 'You have been successfully unsubscribed from all our email campaigns. You will no longer receive marketing emails from us.'
                : 'You have been successfully unsubscribed from this campaign.';

            return back()->with('status', $message);

        } catch (\Exception $e) {
            // return back()->with('error', $e->getMessage());
            return back()->with('error', 'An error occurred while processing your unsubscription request. Please try again.');
        }
    }

    /**
     * Resubscribe an email address
     */
    public function resubscribe(Request $request, $encryptedEmail)
    {
        try {
            // Decrypt the email address
            $email = Crypt::decryptString($encryptedEmail);
            $campaignId = $request->input('campaign_id');

            // Remove the unsubscription record
            $query = DB::table('campaign_unsubscriptions')->where('email', $email);

            if ($campaignId) {
                $query->where('campaign_id', $campaignId);
            } else {
                $query->where('is_global', true);
            }

            $query->delete();

        

            $message = $campaignId
                ? 'You have been successfully resubscribed to this campaign.'
                : 'You have been successfully resubscribed to our email campaigns.';

            return back()->with('status', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while processing your resubscription request. Please try again.');
        }
    }

    /**
     * Generate an encrypted email for unsubscription links
     */
    public static function generateUnsubscribeLink($email, $campaignId = null)
    {
        $encryptedEmail = Crypt::encryptString($email);
        $url = route('campaign.unsubscribe', $encryptedEmail);

        if ($campaignId) {
            $url .= '?campaign_id=' . $campaignId;
        }

        return $url;
    }
}
