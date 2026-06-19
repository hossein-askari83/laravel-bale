<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Infrastructure\Http\Mappers;

use HosseinAskari\LaravelBale\Domain\DTOs\BaleError;
use HosseinAskari\LaravelBale\Domain\DTOs\BaleResponse;
use Illuminate\Http\Client\Response;

final class BaleResponseMapper
{
    public function map(Response $response): BaleResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $response->json() ?? [];

        $errors = $this->extractErrors($payload);
        $statusOk = $response->successful();
        $success = $statusOk && count($errors) === 0;

        return $success
            ? BaleResponse::success($payload)
            : BaleResponse::failure($errors, $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, BaleError>
     */
    private function extractErrors(array $payload): array
    {
        $errorData = $payload['error_data'] ?? $payload['error'] ?? null;

        if ($errorData === null) {
            return [];
        }

        if (is_array($errorData) && array_is_list($errorData)) {
            return array_map(static fn (array $item): BaleError => BaleError::fromArray($item), $errorData);
        }

        if (is_array($errorData)) {
            return [BaleError::fromArray($errorData)];
        }

        return [new BaleError(null, null, (string) $errorData)];
    }
}
