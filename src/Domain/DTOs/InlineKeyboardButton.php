<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Domain\DTOs;

use HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException;

final readonly class InlineKeyboardButton
{
    public function __construct(
        public string $text,
        public ?string $url = null,
        public ?string $copyText = null,
        public ?WebAppInfo $webApp = null,
    ) {
        if ($this->url === null && $this->copyText === null && $this->webApp === null) {
            throw ValidationException::fromMessage('Inline keyboard button must contain one of url, copy_text, or web_app.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'text' => $this->text,
            'url' => $this->url,
            'copy_text' => $this->copyText,
            'web_app' => $this->webApp?->toArray(),
        ], static fn ($value): bool => $value !== null);
    }
}
