<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Contracts;

use HosseinAskari\LaravelBale\Application\MessageBuilder;
use HosseinAskari\LaravelBale\Domain\DTOs\BaleResponse;
use HosseinAskari\LaravelBale\Domain\DTOs\SendMessageRequest;

interface BaleMessageSenderInterface
{
    public function message(?int $botId = null): MessageBuilder;

    public function sendMessage(SendMessageRequest $request): BaleResponse;

    public function uploadFile(string $path): BaleResponse;
}
