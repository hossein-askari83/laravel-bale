<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Application;

use HosseinAskari\LaravelBale\Contracts\BaleClientInterface;
use HosseinAskari\LaravelBale\Contracts\BaleMessageSenderInterface;
use HosseinAskari\LaravelBale\Domain\DTOs\BaleResponse;
use HosseinAskari\LaravelBale\Domain\DTOs\SendMessageRequest;

final readonly class BaleManager implements BaleMessageSenderInterface
{
    public function __construct(
        private BaleClientInterface $client,
        private ?int $defaultBotId = null,
    ) {}

    public function message(?int $botId = null): MessageBuilder
    {
        return new MessageBuilder($this, $botId ?? $this->defaultBotId);
    }

    public function sendMessage(SendMessageRequest $request): BaleResponse
    {
        return $this->client->sendMessage($request);
    }

    public function uploadFile(string $path): BaleResponse
    {
        return $this->client->uploadFile($path);
    }

    public function defaultBotId(): ?int
    {
        return $this->defaultBotId;
    }
}
