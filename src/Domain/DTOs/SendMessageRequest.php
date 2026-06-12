<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Domain\DTOs;

final readonly class SendMessageRequest
{
    public function __construct(
        public int $botId,
        public string $phoneNumber,
        public MessageData $messageData,
        public ?string $requestId = null,
    ) {}
}
