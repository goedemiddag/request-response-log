<?php

namespace Goedemiddag\RequestResponseLog\Middleware;

use Closure;
use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Goedemiddag\RequestResponseLog\ManualRequestResponseLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class ApplicationRequestResponseLogger
{
    public function handle(Request $request, Closure $next, string $vendor = 'app'): Response
    {
        $requestLog = ManualRequestResponseLogger::fromRequest(
            vendor: $vendor,
            request: $request,
            flow: RequestFlow::Incoming,
            requestIdentifier: Context::get('request-identifier'),
        );

        $response = $next($request);

        ManualRequestResponseLogger::fromResponse(
            requestLog: $requestLog,
            response: $response,
        );

        return $response;
    }
}
