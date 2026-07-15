<?php

return [

    'admin' => [
        // Panel path. Should match the panel's ->path().
        'path' => 'admin',

        // Optional panel version metadata exposed through mekaya()->version().
        'version' => 'v2',

        // Optional brand image path (relative to /public). Null falls back to the bundled mekaya icon.
        'brand' => null,

        // Brand logo height in the panel header.
        'brand_logo_height' => '2rem',

        // Favicon path (relative to /public).
        'favicon' => 'vendor/mekaya/mekaya-icon.svg',
    ],

    'settings' => [
        'name' => env('APP_NAME', 'Mekaya'),
        'email' => env('MAIL_FROM_ADDRESS', 'admin@admin.com'),
    ],

];
