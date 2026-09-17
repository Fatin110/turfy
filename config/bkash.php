<?php

return [
    // Toggle to false and wire up a real gateway implementation once
    // bKash merchant (Tokenized Checkout) credentials are available.
    'mock' => env('BKASH_MOCK', true),

    'base_url' => env('BKASH_BASE_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'),
    'app_key' => env('BKASH_APP_KEY'),
    'app_secret' => env('BKASH_APP_SECRET'),
    'username' => env('BKASH_USERNAME'),
    'password' => env('BKASH_PASSWORD'),
];
