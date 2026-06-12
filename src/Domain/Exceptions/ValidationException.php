<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Domain\Exceptions;

final class ValidationException extends BaleException
{
    public static function fromMessage(string $message): self
    {
        return new self($message, 422);
    }
}
