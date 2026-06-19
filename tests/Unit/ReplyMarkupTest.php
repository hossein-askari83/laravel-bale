<?php

declare(strict_types=1);

use HosseinAskari\LaravelBale\Domain\DTOs\InlineKeyboardButton;
use HosseinAskari\LaravelBale\Domain\DTOs\ReplyMarkup;
use HosseinAskari\LaravelBale\Domain\DTOs\WebAppInfo;
use HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException;

it('serializes inline keyboard to array', function (): void {
    $markup = new ReplyMarkup([
        [
            new InlineKeyboardButton(text: 'Visit', url: 'https://example.com'),
        ],
    ]);

    expect($markup->toArray())->toBe([
        'inline_keyboard' => [
            [
                ['text' => 'Visit', 'url' => 'https://example.com'],
            ],
        ],
    ]);
});

it('serializes button with copy_text', function (): void {
    $button = new InlineKeyboardButton(text: 'Copy', copyText: 'secret-code');

    expect($button->toArray())->toBe([
        'text' => 'Copy',
        'copy_text' => 'secret-code',
    ]);
});

it('serializes button with web_app', function (): void {
    $button = new InlineKeyboardButton(
        text: 'Open App',
        webApp: new WebAppInfo(url: 'https://app.example.com')
    );

    expect($button->toArray())->toBe([
        'text' => 'Open App',
        'web_app' => ['url' => 'https://app.example.com'],
    ]);
});

it('throws when button has no action', function (): void {
    new InlineKeyboardButton(text: 'No action');
})->throws(ValidationException::class, 'Inline keyboard button must contain one of url, copy_text, or web_app');

it('serializes multiple rows', function (): void {
    $markup = new ReplyMarkup([
        [
            new InlineKeyboardButton(text: 'Btn1', url: 'https://a.com'),
            new InlineKeyboardButton(text: 'Btn2', url: 'https://b.com'),
        ],
        [
            new InlineKeyboardButton(text: 'Btn3', copyText: 'copy-me'),
        ],
    ]);

    $result = $markup->toArray();
    expect($result['inline_keyboard'])->toHaveCount(2)
        ->and($result['inline_keyboard'][0])->toHaveCount(2)
        ->and($result['inline_keyboard'][1])->toHaveCount(1);
});
