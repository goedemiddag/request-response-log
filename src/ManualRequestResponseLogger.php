<?php

namespace Goedemiddag\RequestResponseLog;

use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Goedemiddag\RequestResponseLog\Factories\SymfonyRequestLogFactory;
use Goedemiddag\RequestResponseLog\Factories\SymfonyResponseLogFactory;
use Goedemiddag\RequestResponseLog\Models\RequestLog;
use Goedemiddag\RequestResponseLog\Models\ResponseLog;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManualRequestResponseLogger
{
    public static function fromRequest(
        string $vendor,
        Request $request,
        RequestFlow $flow = RequestFlow::Outgoing,
        ?string $requestIdentifier = null,
    ): RequestLog {
        $factory = new SymfonyRequestLogFactory(
            request: $request,
            vendor: $vendor,
            flow: $flow,
            requestIdentifier: $requestIdentifier,
        );

        $requestLog = $factory->build();

        $requestLog->save();

        return $requestLog;
    }

    public static function fromResponse(
        RequestLog $requestLog,
        Response $response,
    ): ResponseLog {
        $factory = new SymfonyResponseLogFactory(
            response: $response,
            requestLog: $requestLog,
        );

        $responseLog = $factory->build();

        $responseLog->save();

        return $responseLog;
    }
}
