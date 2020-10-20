<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'facebook' => [
        'client_id' => envfile('FB_CLIENT_ID'),
        'client_secret' => envfile('FB_CLIENT_SECRET'),
        'redirect' => envfile('FB_CALL_BACK'),
    ],
    'twitter' => [
        'client_id' => envfile('TWITTER_CLIENT_ID'),
        'client_secret' => envfile('TWITTER_CLIENT_SECRET'),
        'redirect' => envfile('TWITTER_CALL_BACK'),
    ],
    'google' => [
        'client_id' => envfile('GOOGLE_CLIENT_ID'),
        'client_secret' => envfile('GOOGLE_CLIENT_SECRET'),
        'redirect' => envfile('GOOGLE_CALL_BACK'),
    ],

];
