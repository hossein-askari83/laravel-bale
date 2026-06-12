<?php

declare(strict_types=1);

use HosseinAskari\LaravelBale\Domain\DTOs\MessageData;
use HosseinAskari\LaravelBale\Domain\DTOs\MessagePayload;
use HosseinAskari\LaravelBale\Domain\DTOs\SendMessageRequest;
use HosseinAskari\LaravelBale\Infrastructure\Http\Requests\SendMessagePayloadBuilder;

it('builds expected send_message payload', function (): void {
    $builder = new SendMessagePayloadBuilder;

    $request = new SendMessageRequest(
        botId: 123456789,
        phoneNumber: '09123456789',
        messageData: new MessageData(
            message: new MessagePayload(text: 'hello'),
            isSecure: true
        ),
        requestId: 'req-1'
    );

    $payload = $builder->build($request);

    expect($payload)->toBe([
        'request_id' => 'req-1',
        'bot_id' => 123456789,
        'phone_number' => '989123456789',
        'message_data' => [
            'message' => ['text' => 'hello'],
            'is_secure' => true,
        ],
    ]);
});
