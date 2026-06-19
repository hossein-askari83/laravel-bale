<?php

declare(strict_types=1);

use HosseinAskari\LaravelBale\Application\BaleManager;
use HosseinAskari\LaravelBale\Application\MessageBuilder;
use HosseinAskari\LaravelBale\Contracts\BaleClientInterface;
use HosseinAskari\LaravelBale\Domain\DTOs\BaleResponse;
use HosseinAskari\LaravelBale\Domain\DTOs\SendMessageRequest;
use HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException;

it('resolves bot_id from constructor', function (): void {
    $manager = new BaleManager(client: fakeClient(), defaultBotId: 999);
    $builder = $manager->message();

    $reflection = new ReflectionProperty(MessageBuilder::class, 'botId');
    $reflection->setValue($builder, 999);

    expect($reflection->getValue($builder))->toBe(999);
});

it('overrides default bot_id with explicit bot_id', function (): void {
    $manager = new BaleManager(client: fakeClient(), defaultBotId: 999);
    $builder = $manager->message(42);

    $reflection = new ReflectionProperty(MessageBuilder::class, 'botId');
    expect($reflection->getValue($builder))->toBe(42);
});

it('throws when no phone number is provided', function (): void {
    $manager = new BaleManager(client: fakeClient(), defaultBotId: 1);
    $manager->message()->text('hello')->send();
})->throws(ValidationException::class, 'Destination phone number is required');

it('throws when no bot_id is available', function (): void {
    $manager = new BaleManager(client: fakeClient());
    $manager->message()->to('989123456789')->text('hello')->send();
})->throws(ValidationException::class, 'bot_id is required');

it('sends text message via fluent builder', function (): void {
    $captured = null;
    $client = new class($captured) implements BaleClientInterface
    {
        public function __construct(private mixed &$captured) {}

        public function sendMessage(SendMessageRequest $request): BaleResponse
        {
            $this->captured = $request;

            return BaleResponse::success(['message_id' => 'msg-1']);
        }

        public function uploadFile(string $filePath): BaleResponse
        {
            return BaleResponse::success([]);
        }
    };

    $manager = new BaleManager(client: $client, defaultBotId: 42);
    $response = $manager->message()
        ->to('989123456789')
        ->text('hello world')
        ->requestId('req-123')
        ->send();

    expect($response->success)->toBeTrue()
        ->and($response->messageId())->toBe('msg-1');
});

it('builds OTP message payload', function (): void {
    $captured = null;
    $client = new class($captured) implements BaleClientInterface
    {
        public function __construct(private mixed &$captured) {}

        public function sendMessage(SendMessageRequest $request): BaleResponse
        {
            $this->captured = $request;

            return BaleResponse::success(['message_id' => 'msg-otp']);
        }

        public function uploadFile(string $filePath): BaleResponse
        {
            return BaleResponse::success([]);
        }
    };

    $manager = new BaleManager(client: $client, defaultBotId: 42);
    $manager->message()
        ->to('989123456789')
        ->otp('654321')
        ->send();

    expect($captured)->not->toBeNull();

    $data = $captured->messageData->toArray();
    expect($data['otp_message']['otp'])->toBe('654321')
        ->and(array_key_exists('message', $data))->toBeFalse();
});

it('enables secure flag on message', function (): void {
    $captured = null;
    $client = new class($captured) implements BaleClientInterface
    {
        public function __construct(private mixed &$captured) {}

        public function sendMessage(SendMessageRequest $request): BaleResponse
        {
            $this->captured = $request;

            return BaleResponse::success([]);
        }

        public function uploadFile(string $filePath): BaleResponse
        {
            return BaleResponse::success([]);
        }
    };

    $manager = new BaleManager(client: $client, defaultBotId: 42);
    $manager->message()
        ->to('989123456789')
        ->text('secret')
        ->secure()
        ->send();

    expect($captured->messageData->isSecure)->toBeTrue();
});

function fakeClient(): BaleClientInterface
{
    return new class implements BaleClientInterface
    {
        public function sendMessage(SendMessageRequest $request): BaleResponse
        {
            return BaleResponse::success(['message_id' => 'fake']);
        }

        public function uploadFile(string $filePath): BaleResponse
        {
            return BaleResponse::success([]);
        }
    };
}
