<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Domain\Exceptions;

use RuntimeException;

class BaleException extends RuntimeException
{
    /**
     * @param  array<string, mixed>|null  $apiResponse
     */
    public function __construct(
        string $message,
        public readonly ?int $statusCode = null,
        public readonly ?int $errorCode = null,
        public readonly ?array $apiResponse = null,
    ) {
        parent::__construct($message, $errorCode ?? 0);
    }
}
