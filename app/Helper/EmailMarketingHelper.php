<?php

namespace App\Helper;

use App\Models\CampaignLog;
use App\Models\SendGridEvent;
use App\Models\User;
use App\Http\Controllers\CampaignUnsubscriptionController;
use DB;
use Http;
use Illuminate\Http\Request;

class EmailMarketingHelper
{
    private static $dbTable = 'contacts';
    public static function getTags($userId)
    {
        $result = DB::table(self::$dbTable)
            ->selectRaw('jsonb_array_elements_text(tags::jsonb) as tag')
            ->whereRaw("jsonb_typeof(tags::jsonb) = 'array'")
            ->where('user_id', $userId)
            ->whereNotNull('tags')
            ->whereRaw("tags::text != '[]'")
            ->whereRaw("tags::text != ''")
            ->whereRaw("tags::text != 'null'")
            ->groupBy('tag')
            ->pluck('tag');

        return $result;
    }

    public static function getZips($userId)
    {
        $result = DB::table(self::$dbTable)
            ->selectRaw('jsonb_array_elements_text(zip::jsonb) as tag')
            ->whereRaw("jsonb_typeof(zip::jsonb) = 'array'")
            ->where('user_id', $userId)
            ->whereNotNull('zip')
            ->whereRaw("zip::text != '[]'")
            ->whereRaw("zip::text != ''")
            ->whereRaw("zip::text != 'null'")
            ->groupBy('tag')
            ->pluck('tag');

        return $result;
    }

    public static function getCounties($userId)
    {
        $result = DB::table(self::$dbTable)
            ->selectRaw('jsonb_array_elements_text(counties::jsonb) as tag')
            ->whereRaw("jsonb_typeof(counties::jsonb) = 'array'")
            ->where('user_id', $userId)
            ->whereNotNull('counties')
            ->whereRaw("counties::text != '[]'")
            ->whereRaw("counties::text != ''")
            ->whereRaw("counties::text != 'null'")
            ->groupBy('tag')
            ->pluck('tag');

        return $result;
    }

    public static function getDealTypes($userId)
    {
        $result = DB::table(self::$dbTable)
            ->selectRaw('jsonb_array_elements_text(deal_type::jsonb) as tag')
            ->whereRaw("jsonb_typeof(deal_type::jsonb) = 'array'")
            ->where('user_id', $userId)
            ->whereNotNull('deal_type')
            ->whereRaw("deal_type::text != '[]'")
            ->whereRaw("deal_type::text != ''")
            ->whereRaw("deal_type::text != 'null'")
            ->groupBy('tag')
            ->pluck('tag');

        return $result;
    }

    public static function deleteSelectedContacts(Request $request)
    {
        $ids = $request->ids;
        $result = DB::table(self::$dbTable)->whereIn('id', explode(',', $ids))->where('user_id', $request->user()->id)->delete();

        if ($result) {
            return true;
        }

        return false;
    }

    public static $emptyUserImage = '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1687353606253x871065296681565600/face-icon-user-icon-design-user-profile-share-icon-avatar-black-and-white-silhouette-png-clipart.jpg';
    public static $emptyPropertyImage = 'https://119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1650658260022x665905578324893700/coming%20soon3.JPG';

    public static function sendgridPreviewEmailAPI()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sendgrid.com/v3/templates/d-54db12e9983443718ef67a221981cd66/versions/6530e516-3b72-43cc-986e-f239b61275c4',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('MAIL_PASSWORD')
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        if ($response) {

            $res = json_decode($response) ?? null;
            if (isset($res->id)) {
                return $res->html_content;
            }
        }
        return null;

    }

    public static function sendgridMailTemplateJson($propertyInfo, $subject, $title, $userDescription, $userId, $unsubscribeLink = '')
    {
        $user = DB::table('users')->find($userId);
        $companyEmail = $user->company_email;
        $companyLogo = $user->company_logo;
        $companyName = $user->company_name;

        $email = $user->email;
        $userFirstName = $user->first_name;
        $userLastName = $user->last_name;
        $userPhone = $user->phone_number;
        $userCoAddress = $user->street_address;
        $userImage = $user->profile_picture;

        $templateJson = null;
        if (isset($user)) {
            $userEmail = $companyEmail ?? $email;
            $userPhone = $userPhone ?? '';
            $userImage = $userImage ?? self::$emptyUserImage;
            $userCompanyName = $companyName ?? '';
            $userFirstName = $userFirstName ?? '';
            $userLastName = $userLastName ?? '';
            $userCoAddress = $userCoAddress ?? '';
            // $userToken = $user->uuid;

            $propertyImage = ($propertyInfo['full_location'] ?? null) ? explode(',', $propertyInfo['full_location'])[0] ?? '' : self::$emptyPropertyImage;

            $templateJson = [
                "county" => $propertyInfo['county'] ?? '',
                "state" => $propertyInfo['state_or_province'] ?? '',
                "property_url" => url('/property/search/?propertyid=' . $propertyInfo['id']) ?? '',
                "city_state_zip" => ($propertyInfo['city_name'] ?? '') . " " . $propertyInfo['state_or_province'] . " " . $propertyInfo['zip_code'],
                "price" => $propertyInfo['short_list_price'] ?? '',
                "property_img" => $propertyImage,
                "user_description" => $userDescription,
                "user_img" => $userImage,
                "user_name" => $userFirstName . ' ' . $userLastName,
                "user_company" => $userCompanyName,
                "user_phone" => $userPhone,
                "user_email" => $userEmail,
                "user_co_address" => $userCoAddress,
                "beds" => $propertyInfo['bedrooms_count'] ?? '',
                "baths" => $propertyInfo['bathrooms_total_count'] ?? '',
                "sqft" => $propertyInfo['total_finished_sqft'] ?? '',
                "subject" => $subject,
                "header" => $title,
                "unsubscribe" => $unsubscribeLink
            ];
        }

        return $templateJson;
    }

    public static function marketingMailPreviewHtml($propertyInfo, $subject, $title, $userDescription, $userId)
    {
        $templateJson = self::sendgridMailTemplateJson($propertyInfo, $subject, $title, $userDescription, $userId);
        $mailTemplate = self::sendgridPreviewEmailAPI();


        $htmlTemplate = preg_replace_callback(
            '/{{{([^{}]*)}}}/',
            function ($matches) use ($templateJson) {
                return isset($templateJson[$matches[1]]) ? $templateJson[$matches[1]] : $matches[0];
            },
            $mailTemplate
        );

        $htmlTemplate = preg_replace_callback(
            '/{{([^{}]*)}}/',
            function ($matches) use ($templateJson) {
                return isset($templateJson[$matches[1]]) ? $templateJson[$matches[1]] : $matches[0];
            },
            $htmlTemplate
        );

        return $htmlTemplate;

    }


    /**
     * Check if an email is unsubscribed
     */
    public static function isEmailUnsubscribed($email)
    {
        return DB::table('campaign_unsubscriptions')
            ->where('email', $email)
            ->where('is_global', true)
            ->exists();
    }

    public static function sendMarketingEmail($campaign, $recipients)
    {
        ini_set('memory_limit', '8G'); // Set memory limit to 8GB
        set_time_limit(1800); // Set timeout to 1800 seconds (30 minutes)

        $campaignId = $campaign->id;
        $userId = $campaign->user_id;
        $subject = $campaign->subject;
        $title = $campaign->email_header;
        $userDescription = $campaign->email_description;
        $propertyInfo = $campaign->property_data;

        $user = User::find($userId);

        $userName = $user->first_name . ' ' . $user->last_name;
        $userEmail = $user->company_email ?? $user->email;
        $userCompanyName = $user->company_name ?? '';
        $userFirstName = $user->first_name ?? '';
        $userLastName = $user->last_name ?? '';
        $userName = $userFirstName . ' ' . $userLastName;

        $emailArray = [];
        $unsubsribedEmails = [];
        foreach ($recipients as $recipient) {
            // Skip if email is unsubscribed
            if (self::isEmailUnsubscribed($recipient->email)) {
                $unsubsribedEmails[] = $recipient->email;
                continue;
            }


            $unsubscribeLink = CampaignUnsubscriptionController::generateUnsubscribeLink($recipient->email, $campaignId);
            $templateJson = self::sendgridMailTemplateJson(
                $propertyInfo,
                $subject,
                $title,
                $userDescription,
                $userId,
                $unsubscribeLink
            );


            if ($recipient->name) {
                $emailArray[] = [
                    'id' => $recipient->id,
                    'to' => [['name' => $recipient->name, 'email' => $recipient->email]],
                    "subject" => $subject,
                    "dynamic_template_data" => $templateJson
                ];
            } else {
                $emailArray[] = [
                    'id' => $recipient->id,
                    'to' => [['email' => $recipient->email]],
                    "subject" => $subject,
                    "dynamic_template_data" => $templateJson
                ];
            }
        }


        // Update status to unsubscribed for emails that are globally unsubscribed
        if (!empty($unsubsribedEmails)) {
            DB::table('campaign_recipients')
                ->where('campaign_id', $campaignId)
                ->whereIn('email', $unsubsribedEmails)
                ->update(['status' => 'unsubscribed', 'updated_at' => DB::raw('NOW()')]);
        }


        if (!empty($emailArray)) {
            $chunksEmails = array_chunk($emailArray, 980);

            foreach ($chunksEmails as $chunkKey => $chunksEmail) {
                $emailJson = [
                    "from" => [
                        "email" => env('MAIL_FROM_ADDRESS'),
                        "name" => $userName
                    ],
                    "reply_to" => [
                        "email" => $userEmail,
                        "name" => $userName
                    ],
                    "personalizations" => $chunksEmail,
                    "template_id" => config('user_email_config.email_marketing.email_marketing_offer.template_id')
                ];

                $httpResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('MAIL_PASSWORD'),
                    'Content-Type' => 'application/json',
                ])
                    ->withOptions([
                        'http_errors' => false, // Don't throw exceptions on HTTP protocol errors
                        'timeout' => 0, // No timeout
                        'allow_redirects' => true,
                    ])
                    ->post('https://api.sendgrid.com/v3/mail/send', $emailJson);

                $http_status = $httpResponse->status();
                $headers = $httpResponse->headers();
                $body = $httpResponse->body();

                DB::table('campaign_recipients')
                    ->whereIn('id', array_column($chunksEmail, 'id'))
                    ->update(['status' => 'sent', 'updated_at' => DB::raw('NOW()'), 'sent_at' => DB::raw('NOW()')]);



                $logArray = [
                    'campaign_id' => $campaignId,
                    'recipients_ids' => json_encode(array_column($chunksEmail, 'id')),
                    'user_id' => $userId,
                    'from' => $userEmail,
                    'created_at' => DB::raw('NOW()'),
                    'updated_at' => DB::raw('NOW()')
                ];

                if ($body) {
                    $res = json_decode($body) ?? null;
                    if (isset($res->errors)) {
                        $logArray['errors'] = ($res->errors[0]->field ?? '') . ': ' . ($res->errors[0]->message ?? '');
                        $logArray['success'] = false;
                        DB::table('campaign_logs')
                            ->insert($logArray);
                        continue;
                    }
                }
                if ($http_status == 200 || $http_status == 202) {
                    $xMessageId = null;
                    if (isset($headers['X-Message-Id'][0])) {
                        $xMessageId = $headers['X-Message-Id'][0];
                    }
                    $logArray['sendgrid_email_id'] = trim($xMessageId);
                    $logArray['success'] = true;
                    DB::table('campaign_logs')
                        ->insert($logArray);
                    continue;
                }
                $logArray['errors'] = 'Unable to send the email';
                $logArray['success'] = false;
                DB::table('campaign_logs')
                    ->insert($logArray);
            }
        }
        return true;
    }



    public static function getSendgridEventByCampaignId($campaignId)
    {
        $campaignLog = DB::table('campaign_logs')->where('campaign_id', $campaignId)->get();
        $events = [];
        foreach ($campaignLog as $log) {
            $events[] = DB::table('sendgrid_events')->where('message_id', $log->sendgrid_email_id)->get();
        }

        return $events;
    }

    /**
     * Get count of SendGrid events by event type for a specific user and campaign
     * 
     * @param string $event Event type (processed, delivered, bounce, open, click, spam report, etc.)
     * @param int $userId User ID
     * @param int|null $campaignId Campaign ID (optional)
     * @return int Count of events
     */
    public static function getCountByEvent($event, $userId, $campaignId)
    {
        $query = DB::table('campaign_logs as cl')
            ->select('cl.id')
            ->leftJoin('sendgrid_events as se', DB::raw('TRIM(cl.sendgrid_email_id)'), '=', 'se.message_id')
            ->where('cl.user_id', $userId)
            ->where('cl.campaign_id', $campaignId)
            ->where('se.event', $event);

        return $query->count();
    }

    /**
     * Get comprehensive event statistics for a campaign
     * 
     * @param int $campaignId Campaign ID
     * @param int $userId User ID
     * @return array Array of event counts
     */
    public static function getCampaignEventStats($campaignId, $userId)
    {
        $events = [
            'processed' => self::getCountByEvent('processed', $userId, $campaignId),
            'delivered' => self::getCountByEvent('delivered', $userId, $campaignId),
            'bounce' => self::getCountByEvent('bounce', $userId, $campaignId),
            'open' => self::getCountByEvent('open', $userId, $campaignId),
            'click' => self::getCountByEvent('click', $userId, $campaignId),
            'spam_report' => self::getCountByEvent('spam report', $userId, $campaignId),
            'unsubscribe' => self::getCountByEvent('unsubscribe', $userId, $campaignId),
            'dropped' => self::getCountByEvent('dropped', $userId, $campaignId),
        ];

        return $events;
    }

    /**
     * Get all SendGrid events for a specific campaign with details
     * 
     * @param int $campaignId Campaign ID
     * @return array Array of events with campaign log details
     */
    public static function getSendgridEventsByCampaignId($campaignId)
    {
        return DB::table('campaign_logs as cl')
            ->select([
                'cl.id as campaign_log_id',
                'cl.campaign_id',
                'cl.user_id',
                'cl.from',
                'cl.success',
                'cl.sendgrid_email_id',
                'cl.created_at as log_created_at',
                'se.id as event_id',
                'se.email',
                'se.event',
                'se.timestamp',
                'se.category',
                'se.status',
                'se.reason',
                'se.response'
            ])
            ->leftJoin('sendgrid_events as se', DB::raw('TRIM(cl.sendgrid_email_id)'), '=', 'se.message_id')
            ->where('cl.campaign_id', $campaignId)
            ->whereNotNull('se.id')
            ->orderBy('se.timestamp', 'desc')
            ->get()
            ->toArray();
    }
}