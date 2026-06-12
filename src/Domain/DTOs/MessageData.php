<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Domain\DTOs;

use HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException;

final readonly class MessageData
{
    public function __construct(
        public ?MessagePayload $message = null,
        public ?OtpMessage $otpMessage = null,
        public bool $isSecure = false,
    ) {
        if ($this->message === null && $this->otpMessage === null) {
            throw ValidationException::fromMessage('message_data requires message or otp_message.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'message' => $this->message?->toArray(),
            'otp_message' => $this->otpMessage?->toArray(),
            'is_secure' => $this->isSecure ?: null,
        ], static fn ($value): bool => $value !== null);
    }
}
