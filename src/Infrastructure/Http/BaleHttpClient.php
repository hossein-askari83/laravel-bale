<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Infrastructure\Http;

use Closure;
use HosseinAskari\LaravelBale\Contracts\BaleClientInterface;
use HosseinAskari\LaravelBale\Domain\DTOs\BaleResponse;
use HosseinAskari\LaravelBale\Domain\DTOs\SendMessageRequest;
use HosseinAskari\LaravelBale\Domain\Exceptions\ApiException;
use HosseinAskari\LaravelBale\Domain\Exceptions\AuthenticationException;
use HosseinAskari\LaravelBale\Domain\Exceptions\BaleException;
use HosseinAskari\LaravelBale\Domain\Exceptions\MaxContactLimitReachedException;
use HosseinAskari\LaravelBale\Domain\Exceptions\NotBaleUserException;
use HosseinAskari\LaravelBale\Domain\Exceptions\PaymentRequiredException;
use HosseinAskari\LaravelBale\Domain\Exceptions\RateLimitException;
use HosseinAskari\LaravelBale\Domain\Exceptions\ValidationException;
use HosseinAskari\LaravelBale\Infrastructure\Http\Mappers\BaleResponseMapper;
use HosseinAskari\LaravelBale\Infrastructure\Http\Requests\SendMessagePayloadBuilder;
use HosseinAskari\LaravelBale\Infrastructure\Http\Requests\UploadFilePayloadBuilder;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;
use Throwable;

final class BaleHttpClient implements BaleClientInterface
{
    /**
     * @param  array<string, mixed>  $config
     * @param  Closure(?string): LoggerInterface  $loggerFactory
     */
    public function __construct(
        private readonly array $config,
        private readonly Closure $loggerFactory,
        private readonly SendMessagePayloadBuilder $sendMessageBuilder = new SendMessagePayloadBuilder,
        private readonly UploadFilePayloadBuilder $uploadFileBuilder = new UploadFilePayloadBuilder,
        private readonly BaleResponseMapper $responseMapper = new BaleResponseMapper,
    ) {}

    public function sendMessage(SendMessageRequest $request): BaleResponse
    {
        $response = $this->request()->post(
            '/send_message',
            $this->sendMessageBuilder->build($request)
        );

        return $this->mapAndThrow($response, 'send_message');
    }

    public function uploadFile(string $filePath): BaleResponse
    {
        $this->uploadFileBuilder->ensureFile($filePath);

        $response = $this->request()
            ->asMultipart()
            ->attach('file', file_get_contents($filePath), basename($filePath))
            ->post('/upload_file');

        return $this->mapAndThrow($response, 'upload_file');
    }

    private function request(): PendingRequest
    {
        $token = (string) ($this->config['token'] ?? '');

        if ($token === '') {
            throw ValidationException::fromMessage('BALE_API_TOKEN is required.');
        }

        $retry = $this->config['retry'] ?? [];
        $times = (int) ($retry['times'] ?? 2);
        $sleep = (int) ($retry['sleep_milliseconds'] ?? 200);

        return Http::baseUrl((string) ($this->config['base_url'] ?? 'https://safir.bale.ai/api/v3'))
            ->acceptJson()
            ->asJson()
            ->timeout((float) ($this->config['timeout'] ?? 15))
            ->retry($times, $sleep, throw: false)
            ->withHeaders(['api-access-key' => $token]);
    }

    private function mapAndThrow(Response $response, string $operation): BaleResponse
    {
        $mapped = $this->responseMapper->map($response);

        $this->logResponse($operation, $response);
        $this->throwForFailure($response);

        if (! $mapped->success) {
            $firstError = $mapped->firstError();
            $message = $firstError === null ? 'Bale API request failed.' : $firstError->description;

            throw $this->exceptionForErrorCode(
                message: $message,
                statusCode: $response->status(),
                errorCode: $firstError?->code,
                apiResponse: $mapped->data,
            );
        }

        return $mapped;
    }

    private function throwForFailure(Response $response): void
    {
        try {
            $response->throw();
        } catch (RequestException $exception) {
            $payload = $response->json();
            $status = $response->status();
            $message = $exception->getMessage();
            $errorCode = is_array($payload) && isset($payload['error_code']) ? (int) $payload['error_code'] : null;

            throw match (true) {
                $status === 401 || $status === 403 => new AuthenticationException($message, $status, $errorCode, is_array($payload) ? $payload : null),
                $status === 422 => new ValidationException($message, $status, $errorCode, is_array($payload) ? $payload : null),
                $status === 429 => new RateLimitException($message, $status, $errorCode, is_array($payload) ? $payload : null),
                default => new ApiException($message, $status, $errorCode, is_array($payload) ? $payload : null),
            };
        } catch (Throwable $throwable) {
            throw new ApiException($throwable->getMessage());
        }
    }

    private function exceptionForErrorCode(
        string $message,
        ?int $statusCode,
        ?int $errorCode,
        ?array $apiResponse,
    ): BaleException {
        return match ($errorCode) {
            17 => new NotBaleUserException($message, $statusCode, $errorCode, $apiResponse),
            20 => new PaymentRequiredException($message, $statusCode, $errorCode, $apiResponse),
            21 => new MaxContactLimitReachedException($message, $statusCode, $errorCode, $apiResponse),
            default => new ApiException($message, $statusCode, $errorCode, $apiResponse),
        };
    }

    private function logResponse(string $operation, Response $response): void
    {
        $logging = $this->config['logging'] ?? [];
        $enabled = (bool) ($logging['enabled'] ?? true);

        if (! $enabled) {
            return;
        }

        $logger = ($this->loggerFactory)($logging['channel'] ?? null);

        $logger->info('Bale API request finished', [
            'operation' => $operation,
            'status' => $response->status(),
        ]);
    }
}
