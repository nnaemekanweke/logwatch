<?php

return [

    /*
    |--------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------
    |
    | Your tenant's LogWatch API key, from Settings > Tenant API Key in
    | the LogWatch dashboard. Never a personal user token — ingestion
    | requires a tenant-scoped key.
    |
    */

    'api_key' => env('LOGWATCH_API_KEY'),

    /*
    |--------------------------------------------------------------------
    | Endpoint
    |--------------------------------------------------------------------
    |
    | The base URL of your LogWatch instance (no trailing slash, no
    | /api/v1 suffix — the client appends that itself).
    |
    */

    'endpoint' => env('LOGWATCH_ENDPOINT', 'http://localhost:8000'),

    /*
    |--------------------------------------------------------------------
    | Log Group / Stream
    |--------------------------------------------------------------------
    |
    | Log groups and streams are created automatically on first push.
    | The stream defaults to the current environment, so "staging" and
    | "production" traffic for the same app land in separate streams
    | within the same group.
    |
    */

    'log_group' => env('LOGWATCH_LOG_GROUP', env('APP_NAME', 'laravel')),

    'log_stream' => env('LOGWATCH_LOG_STREAM', env('APP_ENV', 'production')),

    /*
    |--------------------------------------------------------------------
    | Buffering
    |--------------------------------------------------------------------
    |
    | Log records are buffered in memory and flushed as a single batched
    | request at the end of the request/command, instead of one HTTP
    | call per log line.
    |
    */

    'buffer_size' => env('LOGWATCH_BUFFER_SIZE', 50),

    /*
    |--------------------------------------------------------------------
    | HTTP Timeout
    |--------------------------------------------------------------------
    */

    'timeout' => env('LOGWATCH_TIMEOUT', 5),

];
