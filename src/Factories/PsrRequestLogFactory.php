<?php

namespace Goedemiddag\RequestResponseLog\Factories;

use Goedemiddag\RequestResponseLog\Contracts\Factory;
use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Goedemiddag\RequestResponseLog\Helpers\Sanitizer;
use Goedemiddag\RequestResponseLog\Models\RequestLog;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;

readonly class PsrRequestLogFactory implements Factory
{
    public function __construct(
        private RequestInterface $request,
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
                ->getHeaders(),
            vendor: $this->vendor,
        );
    }

    private function getBaseUri(): string
    {
        $uri = $this
            ->request
            ->getUri();

        return Str::of($uri->getScheme())
            ->append('://')
            ->append($uri->getHost())
            ->toString();
    }

    private function getPath(): string
    {
        return $this
            ->request
            ->getUri()
            ->getPath();
    }

    private function getQueryParameters(): array
    {
        $queryString = $this
            ->request
            ->getUri()
            ->getQuery();

        return Sanitizer::filterSensitiveData(
            array: HeaderUtils::parseQuery($queryString),
            vendor: $this->vendor,
        );
    }

    private function getBody(): array|string
    {
        $contents = $this
            ->request
            ->getBody()
            ->getContents();
        if (! ctype_print($contents)) {
            return '--binary--';
        }

        $contentType = $this
            ->request
            ->getHeader('Content-Type');
        if ($contentType === ['application/x-www-form-urlencoded']) {
            return Sanitizer::filterSensitiveData(
                array: HeaderUtils::parseQuery($contents),
                vendor: $this->vendor,
            );
        }

        return Sanitizer::sanitizeBody(
            body: $contents,
            vendor: $this->vendor,
        );
    }

    private function getRequestIdentifier(): string
    {
        return $this->requestIdentifier ?? Str::uuid()->toString();
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
            'request_identifier' => $this->getRequestIdentifier(),
        ]);
    }
}
