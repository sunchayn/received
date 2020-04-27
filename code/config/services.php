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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sms' => [
        'service' => env('SMS_SERVICE', 'TWILIO'),
        'key' => env('SMS_SERVICE_KEY'),
        'secret' => env('SMS_SERVICE_SECRET'),
    ],

    'twilio' => [
        'verify_service_id' => env('TWILIO_VERIFICATION_SERVICE_ID'),
        'authy_api_key' => env('TWILIO_AUTHY_API_KEY'),
        'authy_email' => env('TWILIO_AUTHY_EMAIL'),
    ]

];
