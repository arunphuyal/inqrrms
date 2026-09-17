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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'pusher' => [
        'instance_id' => env('PUSHER_INSTANCE_ID'),
        'beam_secret' => env('PUSHER_BEAM_SECRET'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization_id' => env('OPENAI_ORGANIZATION_ID'),
        'model' => env('OPENAI_MODEL', 'gpt-4o'),
    ],

    /*
    |--------------------------------------------------------------------------
    | IRD Central Billing Monitoring System (CBMS) - Nepal
    |--------------------------------------------------------------------------
    |
    | Endpoints as published in the IRD "Central Billing Monitoring System
    | API Documentation" (updated 2079 Ashoj 28 / 2022-10-14). Credentials
    | and the seller PAN are configured per restaurant (see CbmsSetting),
    | not here - these are only the fixed service URLs.
    |
    */
    'cbms' => [
        'bill_url' => env('CBMS_BILL_URL', 'https://cbapi.ird.gov.np/api/bill'),
        'bill_return_url' => env('CBMS_BILL_RETURN_URL', 'https://cbapi.ird.gov.np/api/billreturn'),

        // Class implementing App\Services\Cbms\Contracts\NepaliDateConverter,
        // used to render invoice_date / credit_note_date as the BS calendar
        // string CBMS expects. See UnavailableNepaliDateConverter for why
        // this isn't a working implementation out of the box.
        'date_converter' => env('CBMS_DATE_CONVERTER', \App\Services\Cbms\UnavailableNepaliDateConverter::class),
    ],

];
