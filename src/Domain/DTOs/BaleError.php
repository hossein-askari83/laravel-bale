<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Domain\DTOs;

final readonly class BaleError
{
    public function __construct(
        public ?string $phoneNumber,
        public ?int $code,
        public string $description,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            phoneNumber: isset($payload['phone_number']) ? (string) $payload['phone_number'] : null,
            code: isset($payload['code']) ? (int) $payload['code'] : null,
            description: (string) ($payload['description'] ?? 'Unknown Bale API error.'),
        );
    }
}
