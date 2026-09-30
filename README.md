# LogWatch Laravel Client

Push your Laravel app's logs to [LogWatch](https://github.com) as a standard
Laravel log channel — no new logging API to learn, just `Log::info(...)` like
always.

**Requires Laravel 10, 11, 12, or 13, on PHP 8.1+.** Laravel 8 and 9 are past
their security-support window (Composer's own installer now refuses to
install them at all — every release is flagged by an unpatched advisory), so
they're intentionally not supported. If you're still on one of them, upgrade
Laravel first.

## Installation

```bash
composer require nnaemekanweke/logwatch
php artisan vendor:publish --tag=logwatch-config
```

Add to `.env`:

```env
LOGWATCH_API_KEY=your-tenant-api-key
LOGWATCH_ENDPOINT=https://your-logwatch-instance.example.com
LOGWATCH_LOG_GROUP="${APP_NAME}"
LOGWATCH_LOG_STREAM="${APP_ENV}"
```

Get `LOGWATCH_API_KEY` from your LogWatch dashboard under **Settings > Tenant
API Key** — it's shared across your whole tenant, not tied to one user.

## Usage

Register the channel in `config/logging.php`:

```php
'channels' => [
    // ...

    'logwatch' => [
        'driver' => 'custom',
        'via' => \Nnaemekanweke\Logwatch\Logging\LogWatchChannelFactory::class,
    ],
],
```

Then either log straight to it:

```php
Log::channel('logwatch')->info('Order shipped', ['order_id' => $order->id]);
```

or add it to your `stack` channel so every log line goes to LogWatch
alongside your normal log file:

```php
'stack' => [
    'driver' => 'stack',
    'channels' => ['single', 'logwatch'],
],
```

```env
LOG_STACK=stack
```

## How it works

- Log groups and streams are created automatically on first push (like
  CloudWatch's `PutLogEvents`) — nothing to provision up front.
- Records are buffered in memory and flushed as a single batched API call at
  the end of the request or command, instead of one HTTP call per log line.
- Delivery failures (network errors, LogWatch downtime) are caught and
  swallowed — a broken connection to LogWatch will never break your app or
  throw from a log call.

## Configuration reference

| Env var | Default | Description |
|---|---|---|
| `LOGWATCH_API_KEY` | — | Required. Your tenant's API key. |
| `LOGWATCH_ENDPOINT` | `http://localhost:8000` | Base URL of your LogWatch instance. |
| `LOGWATCH_LOG_GROUP` | `APP_NAME` | Log group name (auto-created). |
| `LOGWATCH_LOG_STREAM` | `APP_ENV` | Log stream name (auto-created). |
| `LOGWATCH_BUFFER_SIZE` | `50` | Records buffered before an early flush. |
| `LOGWATCH_TIMEOUT` | `5` | HTTP timeout in seconds. |

## Testing

```bash
composer install
vendor/bin/phpunit
```
