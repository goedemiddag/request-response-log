<?php

namespace Goedemiddag\RequestResponseLog\Middleware;

use Closure;
use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Goedemiddag\RequestResponseLog\ManualRequestResponseLogger;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationRequestResponseLogger
{
    public function handle(Request $request, Closure $next, string $vendor = 'app'): Response
    {
        $requestLog = ManualRequestResponseLogger::fromRequest(
            vendor: $vendor,
            request: $request,
            flow: RequestFlow::Incoming,
        );

        $response = $next($request);

        ManualRequestResponseLogger::fromResponse(
            requestLog: $requestLog,
            response: $response,
        );

        return $response;
    }
}
