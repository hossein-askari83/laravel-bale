<?php

declare(strict_types=1);

return [
    'token' => env('BALE_API_TOKEN'),

    'base_url' => env('BALE_BASE_URL', 'https://safir.bale.ai/api/v3'),

    'default_bot_id' => env('BALE_DEFAULT_BOT_ID'),

    'timeout' => (float) env('BALE_TIMEOUT', 15),

    'retry' => [
        'times' => (int) env('BALE_RETRY_TIMES', 2),
        'sleep_milliseconds' => (int) env('BALE_RETRY_SLEEP_MS', 200),
    ],

    'logging' => [
        'enabled' => (bool) env('BALE_LOGGING_ENABLED', true),
        'channel' => env('BALE_LOG_CHANNEL'),
    ],
];
