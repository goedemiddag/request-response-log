<?php

namespace Goedemiddag\RequestResponseLog;

use Closure;
use Goedemiddag\RequestResponseLog\Contracts\BacktraceResolver;
use Goedemiddag\RequestResponseLog\Factories\PsrRequestLogFactory;
use Goedemiddag\RequestResponseLog\Factories\PsrResponseLogFactory;
use Goedemiddag\RequestResponseLog\Support\BacktraceResolvers\IgnoredResolver;
use Illuminate\Support\Facades\Context;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RequestResponseLogger
{
    /**
     * @param string $vendor
     * @param BacktraceResolver $backtraceResolver
     * @param array|Closure():array|null $context
     * @return callable
     */
    public static function middleware(string $vendor, BacktraceResolver $backtraceResolver = new IgnoredResolver(), Closure|array|null $context = []): callable
    {
        return static function (callable $handler) use ($vendor, $backtraceResolver, $context): callable {
            return static function (RequestInterface $request, array $options = []) use ($vendor, $backtraceResolver, $context, $handler) {
                $requestLogFactory = new PsrRequestLogFactory(
                    request: $request,
                    vendor: $vendor,
                    requestIdentifier: Context::get('request-identifier'),
                    context: $context,
                );

                $requestLog = $requestLogFactory->build();
                $requestLog->backtrace = $backtraceResolver->get();
                $requestLog->save();

                return $handler($request, $options)->then(
                    function (ResponseInterface $response) use ($requestLog): ResponseInterface {
                        $responseLogFactory = new PsrResponseLogFactory(
                            response: $response,
                            requestLog: $requestLog,
                        );

                        $responseLog = $responseLogFactory->build();

                        $responseLog->save();

                        return $response;
                    },
                );
            };
        };
    }
}
