<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response as PsrResponse;
use HosseinAskari\LaravelBale\Infrastructure\Http\Mappers\BaleResponseMapper;
use Illuminate\Http\Client\Response;

it('maps successful response', function (): void {
    $mapper = new BaleResponseMapper;

    $response = new Response(new PsrResponse(200, [], json_encode([
        'message_id' => 'msg-1',
        'error_data' => null,
    ])));

    $mapped = $mapper->map($response);

    expect($mapped->success)->toBeTrue()
        ->and($mapped->data['message_id'])->toBe('msg-1')
        ->and($mapped->errors)->toBe([]);
});

it('maps error_data as failure', function (): void {
    $mapper = new BaleResponseMapper;

    $response = new Response(new PsrResponse(200, [], json_encode([
        'message_id' => null,
        'error_data' => [[
            'phone_number' => '989123456789',
            'code' => 8,
            'description' => 'Invalid phone',
        ]],
    ])));

    $mapped = $mapper->map($response);

    expect($mapped->success)->toBeFalse()
        ->and($mapped->errors[0]->code)->toBe(8);
});
