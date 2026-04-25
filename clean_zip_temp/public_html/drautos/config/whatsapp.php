<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp API Configuration
    |--------------------------------------------------------------------------
    |
    | These values are pulled directly from your .env file.
    |
    */

    'instance_id'  => env('WHATSAPP_INSTANCE_ID'),
    'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
    'base_url'     => env('WHATSAPP_BASE_URL', 'https://wa2.shnaveed.com/api'),
];
