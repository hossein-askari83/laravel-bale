<?php

declare(strict_types=1);

use HosseinAskari\LaravelBale\Domain\DTOs\BaleError;
use HosseinAskari\LaravelBale\Domain\DTOs\BaleResponse;

it('returns message_id from data', function (): void {
    $response = BaleResponse::success(['message_id' => 'msg-42']);
    expect($response->messageId())->toBe('msg-42');
});

it('returns null message_id when missing', function (): void {
    $response = BaleResponse::success([]);
    expect($response->messageId())->toBeNull();
});

it('returns file_id from data', function (): void {
    $response = BaleResponse::success(['file_id' => 'file-99']);
    expect($response->fileId())->toBe('file-99');
});

it('returns null file_id when missing', function (): void {
    $response = BaleResponse::success([]);
    expect($response->fileId())->toBeNull();
});

it('reports errors correctly', function (): void {
    $error = new BaleError('989123456789', 8, 'Invalid phone');
    $response = BaleResponse::failure([$error], ['error_data' => []]);

    expect($response->hasErrors())->toBeTrue()
        ->and($response->firstError())->toBe($error)
        ->and($response->success)->toBeFalse();
});

it('firstError returns null when no errors', function (): void {
    $response = BaleResponse::success([]);
    expect($response->firstError())->toBeNull()
        ->and($response->hasErrors())->toBeFalse();
});
