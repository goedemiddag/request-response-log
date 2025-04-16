<?php

namespace Goedemiddag\RequestResponseLog\Factories;

use Goedemiddag\RequestResponseLog\Contracts\Factory;
use Goedemiddag\RequestResponseLog\Helpers\Sanitizer;
use Goedemiddag\RequestResponseLog\Models\RequestLog;
use Goedemiddag\RequestResponseLog\Models\ResponseLog;
use Symfony\Component\HttpFoundation\Response;

readonly class SymfonyResponseLogFactory implements Factory
{
    public function __construct(
        private Response $response,
        private RequestLog $requestLog,
    ) {
    }

    private function getRequestLogId(): string
    {
        return $this
            ->requestLog
            ->id;
    }

    private function indicateSuccess(): bool
    {
        return $this
            ->response
            ->isSuccessful();
    }

    private function getStatusCode(): int
    {
        return $this
            ->response
            ->getStatusCode();
    }

    private function getReasonPhrase(): string
    {
        return Response::$statusTexts[$this->response->getStatusCode()] ?? '';
    }

    private function getHeaders(): array
    {
        return Sanitizer::filterSensitiveData(
            array: $this
                ->response
                ->headers
                ->all(),
            vendor: $this
                ->requestLog
                ->vendor,
        );
    }

    private function getBody(): array|string
    {
        $contents = $this
            ->response
            ->getContent();

        return Sanitizer::isBinary($contents)
            ? '--binary--'
            : Sanitizer::sanitizeBody(
                body: $contents,
                vendor: $this
                    ->requestLog
                    ->vendor,
            );
    }

    public function build(): ResponseLog
    {
        return new ResponseLog([
            'request_log_id' => $this->getRequestLogId(),
            'success' => $this->indicateSuccess(),
            'status_code' => $this->getStatusCode(),
            'reason_phrase' => $this->getReasonPhrase(),
            'headers' => $this->getHeaders(),
            'body' => $this->getBody(),
        ]);
    }
}
