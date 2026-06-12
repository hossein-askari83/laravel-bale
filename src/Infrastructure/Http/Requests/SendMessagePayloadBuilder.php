<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Infrastructure\Http\Requests;

use HosseinAskari\LaravelBale\Domain\DTOs\SendMessageRequest;
use HosseinAskari\LaravelBale\Support\PhoneNumberNormalizer;

final class SendMessagePayloadBuilder
{
    /**
     * @return array<string, mixed>
     */
    public function build(SendMessageRequest $request): array
    {
        return array_filter([
            'request_id' => $request->requestId,
            'bot_id' => $request->botId,
            'phone_number' => PhoneNumberNormalizer::normalize($request->phoneNumber),
            'message_data' => $request->messageData->toArray(),
        ], static fn ($value): bool => $value !== null);
    }
}
