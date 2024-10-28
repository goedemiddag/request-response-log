<?php

namespace Goedemiddag\RequestResponseLog\Tests;

use Goedemiddag\RequestResponseLog\Models\RequestLog;
use Goedemiddag\RequestResponseLog\RequestResponseLogger;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class RequestResponseLoggerTest extends TestCase
{
    public function test_it_logs_the_request_and_response(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"hello": "World!"}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(RequestResponseLogger::middleware('middleware-test-vendor'));

        $client = new Client(['handler' => $handlerStack]);
        $client->request(
            method: 'PATCH',
            uri: 'https://echo.hoppscotch.io/middleware?test=true',
            options: [
                'headers' => ['Accept' => 'application/json'],
                'json' => ['hello' => 'world'],
            ],
        );

        $this->assertDatabaseHas(RequestLog::class, [
            'flow' => 'out',
            'vendor' => 'middleware-test-vendor',
            'method' => 'PATCH',
            'headers' => json_encode(['Content-Length' => ['17'], 'User-Agent' => ['GuzzleHttp/7'], 'Content-Type' => ['application/json'], 'Host' => ['echo.hoppscotch.io'], 'Accept' => ['application/json']]),
            'base_uri' => 'https://echo.hoppscotch.io',
            'path' => '/middleware',
            'query_parameters' => json_encode(['test' => 'true']),
            'body' => json_encode(['hello' => 'world']),
        ]);
    }
}
