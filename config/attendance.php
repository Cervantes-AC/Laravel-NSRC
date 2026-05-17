<?php

return [
    'google_sheets' => [
        'api_url' => env(
            'GOOGLE_SHEETS_API_URL',
            'https://script.google.com/macros/s/AKfycbwlIBr9rL3NM7hK6XWc0eiwmck1xzFhKCqrKYcA08x5MtH5wbTad47dNw7CMX_PAYlX/exec'
        ),
        'request_timeout' => (int) env('GOOGLE_SHEETS_TIMEOUT', 30),
        'sync_schedule' => env('GOOGLE_SHEETS_SYNC_SCHEDULE', 'hourly'),
    ],
    'name_merging' => [
        'enabled' => env('ATTENDANCE_NAME_MERGING', true),
        'similarity_threshold' => env('ATTENDANCE_SIMILARITY_THRESHOLD', 85.0),
        'fuzzy_threshold' => env('ATTENDANCE_FUZZY_THRESHOLD', 70.0),
    ],
    'sectors' => [
        'Main Gate' => 'Security',
        'Admin Office' => 'Administration',
        'Clinic' => 'Health Services',
        'Canteen' => 'Food Services',
        'Dormitory' => 'Accommodation',
    ],
    'default_sector' => 'General',
    'ai' => [
        'default_provider' => env('AI_PROVIDER', 'groq'),
        'groq_api_key_1' => env('GROQ_API_KEY_1'),
        'groq_api_key_2' => env('GROQ_API_KEY_2'),
        'openrouter_api_key_1' => env('OPENROUTER_API_KEY_1'),
        'openrouter_api_key_2' => env('OPENROUTER_API_KEY_2'),
        'google_genai_api_key' => env('GOOGLE_GENAI_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'max_tokens' => env('AI_MAX_TOKENS', 1024),
        'temperature' => env('AI_TEMPERATURE', 0.7),
    ],
    'rate_limiting' => [
        'max_requests_per_minute' => env('RATE_LIMIT_MAX', 100),
    ],
    'backup' => [
        'retention_days' => env('BACKUP_RETENTION_DAYS', 30),
        'database_schedule' => env('BACKUP_DB_SCHEDULE', 'weekly'),
        'files_schedule' => env('BACKUP_FILES_SCHEDULE', 'weekly'),
        'full_schedule' => env('BACKUP_FULL_SCHEDULE', 'monthly'),
        'email_notifications' => env('BACKUP_EMAIL_NOTIFICATIONS', true),
    ],
    'session' => [
        'timeout_minutes' => env('SESSION_TIMEOUT', 60),
        'max_concurrent' => env('MAX_CONCURRENT_SESSIONS', 1),
    ],
    'password_policy' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_number' => true,
        'require_special' => true,
    ],
];
