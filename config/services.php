<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'test' => env('STRIPE_TEST'),
        'monthly' => env('STRIPE_MONTHLY'),
        'yearly' => env('STRIPE_YEARLY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],
    'sendgrid' => [
        'api_key' => env('MAIL_PASSWORD'),
        'webhook_secret' => env('SENDGRID_WEBHOOK_SECRET'),
    ],
    'google_maps' => [
        'api_key' => env('GOOGLE_MAP_API'),
    ],
    'zillow' => [
        'host' => env('ZILLOW_RAPID_HOST'),
        'key' => env('ZILLOW_RAPID_KEY')
    ],
    'microbilt' => [
        'client' => env('MICROBILT_CLIENT_ID'),
        'secret' => env('MICROBILT_CLIENT_SECRET')
    ],
    'db_api' => [
        'key' => env('DB_API_KEY'),
        'url' => env('DB_API_URL')
    ],
    'chat_gpt' => [
        'key' => env('CHATGPT_KEY')
    ],
    'pdffiller' => [
        'api_key' => env('PDFFILLER_API_KEY'),
    ],
    'airtable' => [
        'token' => env('AIRTABLE_PAT'),
        'base_id' => env('AIRTABLE_BASE_ID'),
        'podcast_table' => env('AIRTABLE_PODCAST_TABLE', 'Shows/Episodes'),
        'podcast_view' => env('AIRTABLE_PODCAST_VIEW'),
    ],

];
