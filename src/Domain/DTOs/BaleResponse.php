<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Domain\DTOs;

final readonly class BaleResponse
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, BaleError>  $errors
     */
    public function __construct(
        public bool $success,
        public array $data = [],
        public array $errors = [],
    ) {}

    public static function success(array $payload): self
    {
        return new self(true, $payload);
    }

    /**
     * @param  array<int, BaleError>  $errors
     * @param  array<string, mixed>  $payload
     */
    public static function failure(array $errors, array $payload = []): self
    {
        return new self(false, $payload, $errors);
    }

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
}
