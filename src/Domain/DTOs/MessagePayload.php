<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Domain\DTOs;

use HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException;

final readonly class MessagePayload
{
    public function __construct(
        public ?string $text = null,
        public ?string $fileId = null,
        public ?string $copyText = null,
        public ?ReplyMarkup $replyMarkup = null,
    ) {
        if ($this->text === null && $this->fileId === null) {
            throw ValidationException::fromMessage('Message payload requires text or file_id.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'text' => $this->text,
            'file_id' => $this->fileId,
            'copy_text' => $this->copyText,
            'reply_markup' => $this->replyMarkup?->toArray(),
        ], static fn ($value): bool => $value !== null);
    }
}
