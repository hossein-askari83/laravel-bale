# Laravel Bale — Refactor & Production-Readiness Plan

## Overall Assessment

The codebase is **well-architected** with clean domain-driven layers (Domain, Application, Infrastructure, Contracts, Support). It maps well to the Safir API docs. However, there are **leftover scaffolding artifacts, unused config, thin test coverage, and missing convenience APIs** that should be addressed before university presentation and Packagist publication.

---

## 1. Remove Unused / Leftover Scaffolding Files

### 1a. Delete `configure.php`
- This is the Spatie package skeleton's interactive setup script. It has no purpose in a published package.

### 1b. Fix `LICENSE.md` placeholder
- Line 3: `Copyright (c) :vendor_name` — replace with `Copyright (c) Hossein Askari` and real email.

### 1c. Remove `webhook` config key
- `config/bale.php:24-26` — the `webhook.secret` key has no corresponding implementation. Remove it from config and README. It's premature and misleading.

### 1d. Remove the Farsi docs file from the repo
- `مستندات سفیر بله.md` should not ship with the package. Add it to `.gitattributes` export-ignore (or delete it if it's only for reference). It's already in `.gitattributes` style — add explicit line.

---

## 2. Fix `composer.json` Metadata

### 2a. Fix author email
- `"email": "hossein@example.com"` → use real email `1383.hosseinaskari@gmail.com` (from LICENSE).

### 2b. Add `"support"` section
- Add `issues`, `source` URLs for Packagist presentation.

---

## 3. Fix `BaleManager::defaultBotId` Type

- **`src/Application/BaleManager.php:17`** — `private mixed $defaultBotId = null` should be `private ?int $defaultBotId = null`.
- Remove the `is_numeric()` workaround on line 22; since config casts it through the service provider, just cast once during construction.
- This makes the type system honest and removes a runtime type check.

---

## 4. Make `requireBotId` Private

- **`src/Application/BaleManager.php:37`** — `requireBotId()` is only called from `MessageBuilder`. It should be `private`, not `public`.

---

## 5. Add Convenience Methods to `BaleResponse`

Add to `src/Domain/DTOs/BaleResponse.php`:

```php
public function messageId(): ?string
{
    return $this->data['message_id'] ?? null;
}

public function fileId(): ?string
{
    return $this->data['file_id'] ?? null;
}

public function firstError(): ?BaleError
{
    return $this->errors[0] ?? null;
}

public function hasErrors(): bool
{
    return $this->errors !== [];
}
```

This improves DX significantly — users won't need to manually dig into `$response->data['message_id']`.

---

## 6. Remove `BaleMessageSenderInterface::uploadFile` Method

- **`src/Contracts/BaleMessageSenderInterface.php:17`** — The interface name is `BaleMessageSenderInterface`. File uploading is not "message sending." The `BaleManager` can keep `uploadFile()` as a concrete method. The interface should only declare `message()` and `sendMessage()`.
- Update `BaleServiceProvider` accordingly (the alias still works since BaleManager implements the interface).

---

## 7. Add Specific Exception Classes for API Error Codes

The Safir API defines specific error codes. Add typed exceptions that map to them:

| Code | Exception Class |
|------|----------------|
| 17 | `NotBaleUserException` |
| 20 | `PaymentRequiredException` |
| 21 | `MaxContactLimitReachedException` |

Update `BaleHttpClient::throwForFailure()` and `mapAndThrow()` to throw these when the API error code matches.

These extend `BaleException` and carry the same properties — they just allow users to `catch` specifically.

---

## 8. Expand Test Coverage

Add the following tests (using Pest + Http::fake):

| Test File | Coverage |
|-----------|----------|
| `tests/Unit/PhoneNumberNormalizerTest.php` | 09xx→98 conversion, +98 prefix, invalid numbers |
| `tests/Unit/MessageBuilderTest.php` | Fluent API, OTP path, secure flag, validation errors |
| `tests/Unit/ReplyMarkupTest.php` | toArray, InlineKeyboardButton validation |
| `tests/Feature/SendMessageTest.php` | Add: OTP message, secure message, inline keyboard, file+caption |
| `tests/Feature/UploadFileTest.php` | Upload success, invalid path, error response |
| `tests/Unit/BaleResponseTest.php` | New convenience methods, error extraction |

---

## 9. Remove `.gitattributes` References to Non-Existent Files

- Line 14: `/.php_cs.dist.php` — doesn't exist in the project.
- Line 16: `/psalm.xml` — doesn't exist (project uses PHPStan).
- Line 17: `/psalm.xml.dist` — doesn't exist.
- Line 18: `/testbench.yaml` — doesn't exist.
- Line 19: `/UPGRADING.md` — doesn't exist.
- Line 9: `/art` — doesn't exist.
- Line 10: `/docs` — doesn't exist.

Clean these up to avoid confusion.

---

## 10. README Cleanup

- Remove the `webhook` section (lines 164-168) since we're removing that config.
- Update the config example to match the cleaned config.
- Add a "License" badge and Packagist badge.

---

## 11. Minor Code Quality Improvements

### 11a. `BaleException` constructor
- **`src/Domain/Exceptions/BaleException.php:20`** — `parent::__construct($message, $errorCode ?? 0)` uses the API error code as the PHP exception code. This conflates API domain codes with PHP system codes. Change to `parent::__construct($message)` (use default code 0) and keep `$errorCode` as a separate readonly property.

### 11b. `BaleResponse::$raw` is redundant
- `BaleResponse` stores both `$data` and `$raw` which are always the same payload. Remove `$raw` — `$data` serves the same purpose. This simplifies the DTO.

---

## Execution Order

1. Scaffold cleanup (1a, 1b, 1c, 1d, 9)
2. Config & composer.json fixes (2a, 2b, 10)
3. Type fixes (3, 4, 11a)
4. `BaleResponse` enhancements (5, 11b)
5. Interface cleanup (6)
6. New exception classes (7)
7. Tests (8)
8. Verify with `composer test` and `composer analyse`

---

## Files Modified

| File | Action |
|------|--------|
| `configure.php` | DELETE |
| `LICENSE.md` | Fix placeholder |
| `config/bale.php` | Remove `webhook` |
| `composer.json` | Fix email, add support |
| `src/Application/BaleManager.php` | Type fix, make requireBotId private |
| `src/Domain/DTOs/BaleResponse.php` | Add convenience methods, remove `$raw` |
| `src/Domain/Exceptions/BaleException.php` | Fix constructor |
| `src/Contracts/BaleMessageSenderInterface.php` | Remove `uploadFile` |
| `src/Domain/Exceptions/NotBaleUserException.php` | NEW |
| `src/Domain/Exceptions/PaymentRequiredException.php` | NEW |
| `src/Domain/Exceptions/MaxContactLimitReachedException.php` | NEW |
| `src/Infrastructure/Http/BaleHttpClient.php` | Map error codes to new exceptions |
| `.gitattributes` | Clean stale entries, add Persian docs ignore |
| `README.md` | Remove webhook, update config |
| `tests/Unit/PhoneNumberNormalizerTest.php` | NEW |
| `tests/Unit/MessageBuilderTest.php` | NEW |
| `tests/Unit/ReplyMarkupTest.php` | NEW |
| `tests/Feature/UploadFileTest.php` | NEW |
| `tests/Unit/BaleResponseTest.php` | NEW |
