<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Support;

use HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException;

final class PhoneNumberNormalizer
{
    public static function normalize(string $phoneNumber): string
    {
        $digits = preg_replace('/\D+/', '', $phoneNumber) ?? '';

        if (str_starts_with($digits, '09') && strlen($digits) === 11) {
            $digits = '98'.substr($digits, 1);
        }

        if (! preg_match('/^98\d{10}$/', $digits)) {
            throw ValidationException::fromMessage('phone_number must be in 98XXXXXXXXXX format.');
        }

        return $digits;
    }
}
