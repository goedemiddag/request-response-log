<?php

namespace Goedemiddag\RequestResponseLog\Tests\Models;

use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Goedemiddag\RequestResponseLog\Models\RequestLog;
use Goedemiddag\RequestResponseLog\Models\ResponseLog;
use Goedemiddag\RequestResponseLog\Tests\TestCase;
use Illuminate\Support\Carbon;

class RequestLogTest extends TestCase
{
    private RequestLog $requestLog;

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
        $this->requestLog->created_at = Carbon::now()->subYear();
        $this->requestLog->save(['timestamps' => false]);

        ResponseLog::create([
            'request_log_id' => $this->requestLog->id,
            'success' => true,
            'status_code' => 200,
            'reason_phrase' => 'OK',
            'headers' => ['Content-Type' => 'application/json'],
            'body' => ['hello' => 'world'],
        ]);
    }

    public function test_it_relates_to_response(): void
    {
        $this->assertCount(1, $this->requestLog->responses);
    }

    public function test_it_selects_correct_models_for_prune(): void
    {
        $this->assertSame(1, (new RequestLog())->prunable()->count());
    }

    public function test_it_selects_no_module_for_prune_when_disabled(): void
    {
        config(['request-response-log.prune_after_days' => null]);

        $this->assertSame(0, (new RequestLog())->prunable()->count());
    }
}
