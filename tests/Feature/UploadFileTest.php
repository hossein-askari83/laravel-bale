<?php

declare(strict_types=1);

use HosseinAskari\LaravelBale\Facades\Bale;

it('uploads file successfully', function (): void {
    $tmpFile = tempnam(sys_get_temp_dir(), 'bale_test_');
    file_put_contents($tmpFile, 'fake content');

    try {
        \Illuminate\Support\Facades\Http::fake([
            'https://safir.bale.ai/api/v3/upload_file' => \Illuminate\Support\Facades\Http::response([
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
})->throws(\HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException::class, 'is not readable');
