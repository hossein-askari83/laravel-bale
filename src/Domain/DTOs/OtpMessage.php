<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Domain\DTOs;

final readonly class OtpMessage
{
    public function __construct(public string $otp) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return ['otp' => $this->otp];
    }
}
