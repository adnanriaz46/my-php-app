<?php

namespace App\Helper;

use App\Models\User;
use App\Services\EmailPreferenceService;

class PropertyHelper
{

    public static array $propertyTypes = [
        'Detached' => 'Detached',
        'End of Row/Townhouse' => 'End of Row/Townhouse',
        'Garage/Parking Space' => 'Garage/Parking Space',
        'Interior Row/Townhouse' => 'Interior Row/Townhouse',
        'Manufactured' => 'Manufactured',
        'Mobile Pre 1976' => 'Mobile Pre 1976',
        'Other' => 'Other',
        'Penthouse Unit/Flat/Apartme' => 'Penthouse Unit/Flat/Apartme',
        'Twin/Semi-Detached' => 'Twin/Semi-Detached'
    ];
    public static array $investmentStrategy = [
        'fix_and_flip' => 'Fix & Flip',
        'buy_and_hold' => 'Buy & Hold',
        'wholesale' => 'Wholesale',
        'realtor_broker' => 'Realtor / Broker',
        'new_construction' => 'New Construction',
        'land' => 'Land'
    ];

    public static function formatCurrency($amount)
    {
        return number_format($amount, 2);
    }

    public static function sanitizeInput($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }


    public static function getComboboxInvestStrategies(): array
    {
        $data = [];
        foreach (self::$investmentStrategy as $key => $item) {
            $data[] = $item;
        }

        return $data;
    }

    public static function getComboboxPropertyTypes(): array
    {
        $data = [];
        foreach (self::$propertyTypes as $key => $item) {
            $data[] = $item;
        }

        return $data;
    }

    public function sendBuyBoxAlert(User $user)
    {
        $service = app(EmailPreferenceService::class);

        if ($service->isUnsubscribed($user, 'buy_box_alerts')) {
            return;
        }

        // Proceed to send email
    }

    public static function getAskAiInitialSettingsText($listingType)
    {
        if ($listingType == 'Listed') {
            return 'I am going to send you one message now with property details for a specific property record or listing.

Second, a user will send you a prompt with a question or query about the property you have access to info for from the first message.

When you respond, your message should be MARKDOWN formatted and easy readable

Your reply should be as helpful as possible. You can rely on the listing data provided to you AND you may query external sources and knowledge as well. Your job is to be a helpful AI assistant for the user, helping them to make educated decisions on investment property analysis, while always lightly pushing the user towards taking action. The call to action should tell the user if they are interested they should Click the **"Request a showing"** button, or the **"Ask a question"** button, or the **"Instant offer"** button.

If there are multiple messages in the thread, you don\'t need to push a call to action with every reply, just where most fitting. ';
        } else {
            return 'I am going to send you one message now with property details for a specific property record or listing.

Second, a user will send you a prompt with a question or query about the property you have access to info for from the first message.

When you respond, your message should be MARKDOWN formatted and easy readable

Your reply should be as helpful as possible. You can rely on the listing data provided to you AND you may query external sources and knowledge as well. Your job is to be a helpful AI assistant for the user, helping them to make educated decisions on investment property analysis, while always lightly pushing the user towards taking action.

If there are multiple messages in the thread, you don\'t need to push a call to action with every reply, just where most fitting.';
        }

    }
}
