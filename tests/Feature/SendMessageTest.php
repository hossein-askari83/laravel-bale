<?php

declare(strict_types=1);

use HosseinAskari\LaravelBale\Domain\Exceptions\ApiException;
use HosseinAskari\LaravelBale\Domain\Exceptions\AuthenticationException;
use HosseinAskari\LaravelBale\Facades\Bale;
use Illuminate\Support\Facades\Http;

it('sends text message successfully', function (): void {
    Http::fake([
        'https://safir.bale.ai/api/v3/send_message' => Http::response([
            'message_id' => '523e6875-7c41-491b-8460-04b33039d7fc',
            'error_data' => null,
        ], 200),
    ]);

    $response = Bale::message()
        ->to('989123456789')
        ->text('hello')
        ->requestId('req-1')
        ->send();

    expect($response->success)->toBeTrue()
        ->and($response->data['message_id'])->toBe('523e6875-7c41-491b-8460-04b33039d7fc');
});

it('throws authentication exception on unauthorized response', function (): void {
    Http::fake([
        'https://safir.bale.ai/api/v3/send_message' => Http::response([
            'error' => 'Unauthorized',
        ], 401),
    ]);

    Bale::message()
        ->to('989123456789')
        ->text('hello')
        ->send();
})->throws(AuthenticationException::class);

it('throws api exception when safir returns business errors', function (): void {
    Http::fake([
        'https://safir.bale.ai/api/v3/send_message' => Http::response([
            'message_id' => null,
            'error_data' => [[
                'phone_number' => '989123456789',
                'code' => 8,
                'description' => 'Invalid phone number',
            ]],
        ], 200),
    ]);

    Bale::message()
        ->to('989123456789')
        ->text('hello')
        ->send();
})->throws(ApiException::class, 'Invalid phone number');
