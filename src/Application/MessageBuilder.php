<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Application;

use HosseinAskari\LaravelBale\Domain\DTOs\BaleResponse;
use HosseinAskari\LaravelBale\Domain\DTOs\MessageData;
use HosseinAskari\LaravelBale\Domain\DTOs\MessagePayload;
use HosseinAskari\LaravelBale\Domain\DTOs\OtpMessage;
use HosseinAskari\LaravelBale\Domain\DTOs\ReplyMarkup;
use HosseinAskari\LaravelBale\Domain\DTOs\SendMessageRequest;
use HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException;

final class MessageBuilder
{
    private ?string $phoneNumber = null;

    private ?int $botId = null;

    private ?string $requestId = null;

    private ?string $text = null;

    private ?string $fileId = null;

    private ?string $copyText = null;

    private ?ReplyMarkup $replyMarkup = null;

    private ?string $otp = null;

    private bool $secure = false;

    public function __construct(
        private readonly BaleManager $manager,
        ?int $botId = null,
    ) {
        $this->botId = $botId;
    }

    public function to(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function bot(int $botId): self
    {
        $this->botId = $botId;

        return $this;
    }

    public function requestId(string $requestId): self
    {
        $this->requestId = $requestId;

        return $this;
    }

    public function text(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function file(string $fileId): self
    {
        $this->fileId = $fileId;

        return $this;
    }

    public function copyText(string $copyText): self
    {
        $this->copyText = $copyText;

        return $this;
    }

    public function replyMarkup(ReplyMarkup $replyMarkup): self
    {
        $this->replyMarkup = $replyMarkup;

        return $this;
    }

    public function otp(string $otp): self
    {
        $this->otp = $otp;

        return $this;
    }

    public function secure(bool $secure = true): self
    {
        $this->secure = $secure;

        return $this;
    }

    public function send(): BaleResponse
    {
        if ($this->phoneNumber === null) {
            throw ValidationException::fromMessage('Destination phone number is required.');
        }

        $resolvedBotId = $this->botId ?? $this->manager->defaultBotId();

        if ($resolvedBotId === null) {
            throw ValidationException::fromMessage('bot_id is required. Set BALE_DEFAULT_BOT_ID or call ->bot($id).');
        }

        $request = new SendMessageRequest(
            botId: $resolvedBotId,
            phoneNumber: $this->phoneNumber,
            messageData: new MessageData(
                message: ($this->text !== null || $this->fileId !== null)
                    ? new MessagePayload($this->text, $this->fileId, $this->copyText, $this->replyMarkup)
                    : null,
                otpMessage: $this->otp !== null ? new OtpMessage($this->otp) : null,
                isSecure: $this->secure,
            ),
            requestId: $this->requestId,
        );

        return $this->manager->sendMessage($request);
    }
}
