<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Infrastructure\Http\Requests;

use HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException;

final class UploadFilePayloadBuilder
{
    public function ensureFile(string $filePath): void
    {
        if (! is_file($filePath) || ! is_readable($filePath)) {
            throw ValidationException::fromMessage(sprintf('File path "%s" is not readable.', $filePath));
        }
    }
}
