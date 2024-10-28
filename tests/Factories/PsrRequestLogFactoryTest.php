<?php

namespace Goedemiddag\RequestResponseLog\Tests\Factories;

use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Goedemiddag\RequestResponseLog\Factories\PsrRequestLogFactory;
use Goedemiddag\RequestResponseLog\Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;

class PsrRequestLogFactoryTest extends TestCase
{
    public function test_it_can_build_a_request_log(): void
    {
        $factory = new PsrRequestLogFactory(
            request: new Request(
                method: 'POST',
                uri: new Uri('https://echo.hoppscotch.io/path?test=1'),
                headers: [
                    'Accept' => 'application/json',
                ],
                body: '{"hello": "world"}',
            ),
            vendor: 'test',
            flow: RequestFlow::Incoming,
            requestIdentifier: 'hello-world',
        );

        $requestLog = $factory->build();

        $requestLog->save();

        $this->assertDatabaseHas('request_logs', [
            'id' => $requestLog->id,
            'flow' => 'in',
            'vendor' => 'test',
            'method' => 'POST',
            'headers' => json_encode(['Host' => ['echo.hoppscotch.io'], 'Accept' => ['application/json']]),
            'base_uri' => 'https://echo.hoppscotch.io',
            'path' => '/path',
            'query_parameters' => json_encode(['test' => '1']),
            'body' => json_encode(['hello' => 'world']),
            'request_identifier' => 'hello-world',
        ]);
    }
}
