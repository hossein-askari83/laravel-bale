<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Application;

use HosseinAskari\LaravelBale\Contracts\BaleClientInterface;
use HosseinAskari\LaravelBale\Contracts\BaleMessageSenderInterface;
use HosseinAskari\LaravelBale\Domain\DTOs\BaleResponse;
use HosseinAskari\LaravelBale\Domain\DTOs\SendMessageRequest;
use HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException;

final readonly class BaleManager implements BaleMessageSenderInterface
{
    public function __construct(
        private BaleClientInterface $client,
        private mixed $defaultBotId = null,
    ) {}

    public function message(?int $botId = null): MessageBuilder
    {
        $resolvedBotId = $botId ?? (is_numeric($this->defaultBotId) ? (int) $this->defaultBotId : null);

        return new MessageBuilder($this, $resolvedBotId);
    }

    public function sendMessage(SendMessageRequest $request): BaleResponse
    {
        return $this->client->sendMessage($request);
    }

    public function uploadFile(string $path): BaleResponse
    {
        return $this->client->uploadFile($path);
    }

    public function requireBotId(?int $botId): int
    {
        if ($botId === null) {
            throw ValidationException::fromMessage('bot_id is required. Set BALE_DEFAULT_BOT_ID or call ->bot($id).');
        }

        return $botId;
    }
}
