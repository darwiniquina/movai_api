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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'tmdb' => [
        'access_token' => env('TMDB_ACCESS_TOKEN'),
        'base_url' => env('TMDB_BASE_URL', 'https://api.themoviedb.org/3'),
        'image_base_url' => env('TMDB_IMAGE_BASE_URL', 'https://image.tmdb.org/t/p'),
    ],

    'cerebras' => [
        'base_url' => env('CEREBRAS_BASE_URL', 'https://api.cerebras.ai/v1/chat/completions'),
        'api_key' => env('CEREBRAS_API_KEY'),
        'model' => env('CEREBRAS_MODEL', 'llama-3.3-70b'),
        'max_completion_tokens' => env('CEREBRAS_MAX_COMPLETION_TOKENS', 1024),
        'max_tokens' => env('CEREBRAS_MAX_TOKENS', 500),
        'temperature' => env('CEREBRAS_TEMPERATURE', 0.2),
        'top_p' => env('CEREBRAS_TOP_P', 1),
        'stream' => env('CEREBRAS_STREAM', false),
    ],

];
