<?php

namespace Goedemiddag\RequestResponseLog;

use Goedemiddag\RequestResponseLog\Factories\PsrRequestLogFactory;
use Goedemiddag\RequestResponseLog\Factories\PsrResponseLogFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RequestResponseLogger
{
    public static function middleware(string $vendor): callable
    {
        return static function (callable $handler) use ($vendor): callable {
            return static function (RequestInterface $request, array $options = []) use ($vendor, $handler) {
                $requestLogFactory = new PsrRequestLogFactory(
                    request: $request,
                    vendor: $vendor,
                );

                $requestLog = $requestLogFactory->build();

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
