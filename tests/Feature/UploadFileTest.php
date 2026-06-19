<?php

declare(strict_types=1);

use HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException;
use HosseinAskari\LaravelBale\Facades\Bale;
use Illuminate\Support\Facades\Http;

it('uploads file successfully', function (): void {
    $tmpFile = tempnam(sys_get_temp_dir(), 'bale_test_');
    file_put_contents($tmpFile, 'fake content');

    try {
        Http::fake([
            'https://safir.bale.ai/api/v3/upload_file' => Http::response([
                'file_id' => 'abc123',
            ], 200),
        ]);

        $response = Bale::uploadFile($tmpFile);

        expect($response->success)->toBeTrue()
            ->and($response->fileId())->toBe('abc123');
    } finally {
        @unlink($tmpFile);
    }
});

it('throws validation exception for non-existent file', function (): void {
    Bale::uploadFile('/tmp/does-not-exist-12345.txt');
})->throws(ValidationException::class, 'is not readable');
