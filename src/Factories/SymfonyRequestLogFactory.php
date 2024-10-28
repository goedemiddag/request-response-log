<?php

namespace Goedemiddag\RequestResponseLog\Factories;

use Goedemiddag\RequestResponseLog\Contracts\Factory;
use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Goedemiddag\RequestResponseLog\Helpers\Sanitizer;
use Goedemiddag\RequestResponseLog\Models\RequestLog;
use Symfony\Component\HttpFoundation\Request;

readonly class SymfonyRequestLogFactory implements Factory
{
    public function __construct(
        private Request $request,
        private string $vendor,
        private RequestFlow $flow = RequestFlow::Outgoing,
        private ?string $requestIdentifier = null,
    ) {
    }

    private function getMethod(): string
    {
        return $this
            ->request
            ->getMethod();
    }

    private function getHeaders(): array
    {
        return Sanitizer::filterSensitiveData(
            array: $this
                ->request
                ->headers
                ->all(),
            vendor: $this->vendor,
        );
    }

    private function getBaseUri(): string
    {
        return $this
            ->request
            ->getSchemeAndHttpHost();
    }

    private function getPath(): string
    {
        return $this
            ->request
            ->getPathInfo();
    }

    private function getQueryParameters(): array
    {
        return Sanitizer::filterSensitiveData(
            array: $this
                ->request
                ->query
                ->all(),
            vendor: $this->vendor,
        );
    }

    private function getBody(): array
    {
        return Sanitizer::filterSensitiveData(
            array: $this
                ->request
                ->getPayload()
                ->all(),
            vendor: $this->vendor,
        );
    }

    public function build(): RequestLog
    {
        return new RequestLog([
            'flow' => $this->flow,
            'vendor' => $this->vendor,
            'method' => $this->getMethod(),
            'headers' => $this->getHeaders(),
            'base_uri' => $this->getBaseUri(),
            'path' => $this->getPath(),
            'query_parameters' => $this->getQueryParameters(),
            'body' => $this->getBody(),
            'request_identifier' => $this->requestIdentifier,
        ]);
    }
}
