<?php

namespace Goedemiddag\RequestResponseLog\Tests\Factories;

use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Goedemiddag\RequestResponseLog\Factories\SymfonyRequestLogFactory;
use Goedemiddag\RequestResponseLog\Factories\SymfonyResponseLogFactory;
use Goedemiddag\RequestResponseLog\Models\RequestLog;
use Goedemiddag\RequestResponseLog\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SymfonyResponseLogFactoryTest extends TestCase
{
    public function generateRequestLog(): RequestLog
    {
        $request = Request::create(
            uri: 'https://echo.hoppscotch.io/path?test=1',
            method: 'POST',
            content: '{"hello": "world"}',
        );
        $request->headers->set('Accept', 'application/json');

        $factory = new SymfonyRequestLogFactory(
            request: $request,
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

        $factory = new SymfonyResponseLogFactory(
            response: new Response(
                content: '{"hello": "world"}',
                status: 200,
                headers: [
                    'Content-Type' => 'application/json',
                ],
            ),
            requestLog: $requestLog,
        );

        $responseLog = $factory->build();

        $responseLog->save();

        $this->assertDatabaseHas('response_logs', [
            'id' => $responseLog->id,
            'request_log_id' => $requestLog->id,
            'status_code' => 200,
            // Ignore the headers as Symfony's Response class adds the date header automatically
            // 'headers' => json_encode(['Content-Type' => ['application/json']]),
            'body' => json_encode(['hello' => 'world']),
        ]);
    }
}
