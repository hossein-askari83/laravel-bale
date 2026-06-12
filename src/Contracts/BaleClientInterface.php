<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Contracts;

use HosseinAskari\LaravelBale\Domain\DTOs\BaleResponse;
use HosseinAskari\LaravelBale\Domain\DTOs\SendMessageRequest;

interface BaleClientInterface
{
    public function sendMessage(SendMessageRequest $request): BaleResponse;

    public function uploadFile(string $filePath): BaleResponse;
}
