<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which AI provider to use for report generation and insights.
    | Supported providers: 'groq', 'openrouter'
    |
    */

    'provider' => env('AI_PROVIDER', 'groq'),

    /*
    |--------------------------------------------------------------------------
    | Groq Configuration
    |--------------------------------------------------------------------------
    */

    'groq' => [
        'api_key_1' => env('GROQ_API_KEY_1'),
        'api_key_2' => env('GROQ_API_KEY_2'),
        'endpoint' => 'https://api.groq.com/openai/v1/chat/completions',
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenRouter Configuration
    |--------------------------------------------------------------------------
    */

    'openrouter' => [
        'api_key_1' => env('OPENROUTER_API_KEY_1'),
        'api_key_2' => env('OPENROUTER_API_KEY_2'),
        'endpoint' => 'https://openrouter.ai/api/v1/chat/completions',
        'model' => env('OPENROUTER_MODEL', 'mistralai/mistral-7b-instruct'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Generation Settings
    |--------------------------------------------------------------------------
    */

    'temperature' => 0.7,
    'max_tokens' => 1024,
];
