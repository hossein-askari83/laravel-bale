<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Domain\DTOs;

final readonly class BaleResponse
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, BaleError>  $errors
     * @param  array<string, mixed>|null  $raw
     */
    public function __construct(
        public bool $success,
        public array $data = [],
        public array $errors = [],
        public ?array $raw = null,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function success(array $payload, ?array $raw = null): self
    {
        return new self(true, $payload, [], $raw);
    }

    /**
     * @param  array<int, BaleError>  $errors
     * @param  array<string, mixed>  $payload
     */
    public static function failure(array $errors, array $payload = [], ?array $raw = null): self
    {
        return new self(false, $payload, $errors, $raw);
    }
}
