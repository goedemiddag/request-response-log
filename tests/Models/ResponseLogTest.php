<?php

namespace Goedemiddag\RequestResponseLog\Tests\Models;

use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Goedemiddag\RequestResponseLog\Models\RequestLog;
use Goedemiddag\RequestResponseLog\Models\ResponseLog;
use Goedemiddag\RequestResponseLog\Tests\TestCase;
use Illuminate\Support\Carbon;

class ResponseLogTest extends TestCase
{
    private RequestLog $requestLog;
    private ResponseLog $responseLog;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestLog = RequestLog::create([
            'flow' => RequestFlow::Incoming,
            'vendor' => 'vendor',
            'method' => 'POST',
            'headers' => ['Content-Type' => 'application/json'],
            'base_uri' => 'https://echo.hoppscotch.io',
            'path' => '/path',
            'query_parameters' => ['test' => 1],
            'body' => ['hello' => 'world'],
            'request_identifier' => 'hello-world',
        ]);
        $this->responseLog = ResponseLog::create([
            'request_log_id' => $this->requestLog->id,
            'success' => true,
            'status_code' => 200,
            'reason_phrase' => 'OK',
            'headers' => ['Content-Type' => 'application/json'],
            'body' => ['hello' => 'world'],
        ]);
    }

    public function test_it_relates_to_request(): void
    {
        $this->assertInstanceOf(RequestLog::class, $this->responseLog->request);
        $this->assertSame($this->requestLog->id, $this->responseLog->request->id);
    }
}
