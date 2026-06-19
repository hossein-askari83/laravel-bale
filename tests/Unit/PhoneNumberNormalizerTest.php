<?php

declare(strict_types=1);

use HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException;
use HosseinAskari\LaravelBale\Support\PhoneNumberNormalizer;

it('converts 09xxxxxxxxx to 98xxxxxxxxxx', function (): void {
    expect(PhoneNumberNormalizer::normalize('09123456789'))->toBe('989123456789');
});

it('passes through 98xxxxxxxxxx unchanged', function (): void {
    expect(PhoneNumberNormalizer::normalize('989123456789'))->toBe('989123456789');
});

it('strips + prefix from +98 numbers', function (): void {
    expect(PhoneNumberNormalizer::normalize('+989123456789'))->toBe('989123456789');
});

it('strips dashes and spaces', function (): void {
    expect(PhoneNumberNormalizer::normalize('0912-345-6789'))->toBe('989123456789');
    expect(PhoneNumberNormalizer::normalize('0912 345 6789'))->toBe('989123456789');
});

it('throws validation exception for short number', function (): void {
    PhoneNumberNormalizer::normalize('0912345');
})->throws(ValidationException::class);

it('throws validation exception for number without 98 prefix after conversion', function (): void {
    PhoneNumberNormalizer::normalize('1234567890');
})->throws(ValidationException::class);
