<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Domain\DTOs;

final readonly class ReplyMarkup
{
    /**
     * @param  array<int, array<int, InlineKeyboardButton>>  $inlineKeyboard
     */
    public function __construct(public array $inlineKeyboard) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'inline_keyboard' => array_map(
                static fn (array $row): array => array_map(
                    static fn (InlineKeyboardButton $button): array => $button->toArray(),
                    $row
                ),
                $this->inlineKeyboard
            ),
        ];
    }
}
