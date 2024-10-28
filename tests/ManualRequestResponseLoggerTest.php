<?php

namespace Goedemiddag\RequestResponseLog\Tests;

use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Goedemiddag\RequestResponseLog\ManualRequestResponseLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManualRequestResponseLoggerTest extends TestCase
{
    public function test_it_logs_a_request(): void
    {
        $request = Request::create(
            uri: 'https://echo.hoppscotch.io/path?test=2',
            method: 'POST',
            content: '{"hello": "manual-request"}',
        );
        $request->headers->set('Accept', 'application/json');

        ManualRequestResponseLogger::fromRequest(
            request: $request,
            vendor: 'test',
            flow: RequestFlow::Incoming,
            requestIdentifier: 'manual-request-log',
        );

        $this->assertDatabaseHas('request_logs', [
            'flow' => 'in',
            'vendor' => 'test',
            'method' => 'POST',
            'headers' => '{"host":["echo.hoppscotch.io"],"user-agent":["Symfony"],"accept":["application\\/json"],"accept-language":["en-us,en;q=0.5"],"accept-charset":["ISO-8859-1,utf-8;q=0.7,*;q=0.7"],"content-type":["application\\/x-www-form-urlencoded"]}',
            'base_uri' => 'https://echo.hoppscotch.io',
            'path' => '/path',
            'query_parameters' => json_encode(['test' => '2']),
            'body' => json_encode(['hello' => 'manual-request']),
            'request_identifier' => 'manual-request-log',
        ]);
    }

    public function test_it_logs_a_response(): void
    {
        $request = Request::create(
            uri: 'https://echo.hoppscotch.io/path?test=2',
            method: 'POST',
            content: '{"hello": "manual-request"}',
        );
        $request->headers->set('Accept', 'application/json');

        $requestLog = ManualRequestResponseLogger::fromRequest(
            vendor: 'test',
            request: $request,
            flow: RequestFlow::Incoming,
            requestIdentifier: 'manual-request-log',
        );

        $response = new Response(
            content: '{"hello": "world"}',
            status: 200,
            headers: [
                'Content-Type' => 'application/json',
            ],
        );

        ManualRequestResponseLogger::fromResponse(
            requestLog: $requestLog,
            response: $response,
        );

        $this->assertDatabaseHas('response_logs', [
            'request_log_id' => $requestLog->id,
            'success' => 1,
            'status_code' => 200,
            'reason_phrase' => 'OK',
            // Ignore the headers as Symfony's Response class adds the date header automatically
            // 'headers' => json_encode(['Content-Type' => 'application/json']),
            'body' => json_encode(['hello' => 'world']),
        ]);
    }
}
