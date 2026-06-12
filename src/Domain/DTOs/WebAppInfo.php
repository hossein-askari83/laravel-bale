<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Domain\DTOs;

final readonly class WebAppInfo
{
    public function __construct(public string $url) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return ['url' => $this->url];
    }
}
