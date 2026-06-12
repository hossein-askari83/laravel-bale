# Laravel Bale (Safir API)

Production-ready Laravel package for Bale Messenger Safir APIs with layered architecture, typed DTOs, robust HTTP handling, and fluent developer experience.

## API understanding (Safir map)

### Authentication
- **Header:** `api-access-key`
- **Source:** Safir panel API access key
- **Scope:** required on every endpoint

### Base URL and transport
- **Base URL:** `https://safir.bale.ai/api/v3`
- **Protocol:** HTTPS
- **Formats:** JSON (`send_message`), multipart/form-data (`upload_file`)

### Endpoints
- `POST /send_message`
  - body: `request_id?`, `bot_id`, `phone_number`, `message_data`
  - `message_data.message`: `text?`, `file_id?`, `copy_text?`, `reply_markup?`
  - `message_data.otp_message`: `otp`
  - `message_data.is_secure`: `bool`
  - success: `message_id`, `error_data: null`
  - partial/business errors: `error_data[]` with `phone_number`, `code`, `description`
- `POST /upload_file`
  - multipart: `file` (max 500MB in docs)
  - success: `file_id`
  - errors: `error` / `error_data`

### Error model
- HTTP-level errors (401/403/422/429/5xx)
- Business errors in response body (`error_data`) even with HTTP 200
- Known Safir codes include internal error, rate limit, invalid input, invalid phone, not bale user, payment required, max contact limit.

### Idempotency
- Optional `request_id` prevents duplicate sends on retries; strongly recommended.

### Gaps in official document
- No explicit webhook/callback API contract in provided document.
- No documented request signature/HMAC format for webhooks.
- No separate environment matrix (sandbox/staging/prod) in provided document.

## Installation

```bash
composer require hossein-askari83/laravel-bale
```

## Configuration

Publish config:

```bash
php artisan vendor:publish --tag="bale-config"
```

`config/bale.php`:

```php
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
    'webhook' => [
        'secret' => env('BALE_WEBHOOK_SECRET'),
    ],
];
```

## Authentication

Set:

```dotenv
BALE_API_TOKEN=your-safir-api-access-key
BALE_DEFAULT_BOT_ID=123456789
```

## Usage examples

### Facade

```php
use HosseinAskari\LaravelBale\Facades\Bale;

$response = Bale::message()
    ->to('989123456789')
    ->text('Hello from Laravel')
    ->requestId((string) Str::uuid())
    ->send();
```

### Dependency injection

```php
use HosseinAskari\LaravelBale\Contracts\BaleMessageSenderInterface;

final class NotifyUserAction
{
    public function __construct(private BaleMessageSenderInterface $bale) {}

    public function execute(string $phone): void
    {
        $this->bale->message()
            ->to($phone)
            ->text('Verification sent')
            ->send();
    }
}
```

## Sending messages

### Text

```php
Bale::message()
    ->to('989123456789')
    ->text('Text message')
    ->send();
```

### OTP

```php
Bale::message()
    ->to('989123456789')
    ->otp('123456')
    ->send();
```

### Secure message

```php
Bale::message()
    ->to('989123456789')
    ->text('Confidential')
    ->secure()
    ->send();
```

## Sending media

```php
$upload = Bale::uploadFile(storage_path('app/public/brochure.pdf'));
$fileId = $upload->data['file_id'];

Bale::message()
    ->to('989123456789')
    ->text('Attachment caption')
    ->file($fileId)
    ->send();
```

## Webhooks

Provided Safir document does not define webhook callback endpoint format/signature.  
This package reserves `bale.webhook.secret` for when Safir webhook verification specs are available.

## Error handling

Typed exceptions:
- `BaleException`
- `ApiException`
- `AuthenticationException`
- `ValidationException`
- `RateLimitException`

Each includes message, status code, API error code, and API payload where available.

## Testing

```bash
composer test
```

Tests use Pest + Laravel Testbench and mock external HTTP via `Http::fake()`.

## Contribution guide

1. Fork the repository.
2. Create a feature branch.
3. Run `composer test` and `composer analyse`.
4. Open a pull request with clear change notes.

## License

MIT. See [LICENSE.md](LICENSE.md).
