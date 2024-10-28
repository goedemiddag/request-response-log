<?php

namespace Goedemiddag\RequestResponseLog\Tests\Factories;

use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Goedemiddag\RequestResponseLog\Factories\PsrRequestLogFactory;
use Goedemiddag\RequestResponseLog\Factories\PsrResponseLogFactory;
use Goedemiddag\RequestResponseLog\Models\RequestLog;
use Goedemiddag\RequestResponseLog\Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;

class PsrResponseLogFactoryTest extends TestCase
{
    private function generateRequestLog(): RequestLog
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

        return $requestLog;
    }

    public function test_it_can_build_a_response_log(): void
    {
        $requestLog = $this->generateRequestLog();

        $factory = new PsrResponseLogFactory(
            response: new Response(
                status: 200,
                headers: [
                    'Content-Type' => 'application/json',
                ],
                body: '{"hello": "world"}',
            ),
            requestLog: $requestLog,
        );

        $responseLog = $factory->build();

        $responseLog->save();

        $this->assertDatabaseHas('response_logs', [
            'id' => $responseLog->id,
            'request_log_id' => $requestLog->id,
            'status_code' => 200,
            'headers' => json_encode(['Content-Type' => ['application/json']]),
            'body' => json_encode(['hello' => 'world']),
        ]);
    }
}
