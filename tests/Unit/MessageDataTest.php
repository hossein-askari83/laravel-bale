<?php

declare(strict_types=1);

use HosseinAskari\LaravelBale\Domain\DTOs\MessageData;
use HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException;

it('rejects message_data without message and otp_message', function (): void {
    new MessageData;
})->throws(ValidationException::class);
