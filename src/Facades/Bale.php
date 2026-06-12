<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \HosseinAskari\LaravelBale\Application\MessageBuilder message(?int $botId = null)
 * @method static \HosseinAskari\LaravelBale\Domain\DTOs\BaleResponse sendMessage(\HosseinAskari\LaravelBale\Domain\DTOs\SendMessageRequest $request)
 * @method static \HosseinAskari\LaravelBale\Domain\DTOs\BaleResponse uploadFile(string $path)
 */
final class Bale extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'bale';
    }
}
