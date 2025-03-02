<?php

namespace Goedemiddag\RequestResponseLog\Tests\Models;

use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Goedemiddag\RequestResponseLog\Models\RequestLog;
use Goedemiddag\RequestResponseLog\Models\ResponseLog;
use Goedemiddag\RequestResponseLog\Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class RequestLogTest extends TestCase
{
    private RequestLog $requestLog;

    private function storeRequestLog(Carbon $createdAt): RequestLog
    {
        $requestLog = RequestLog::create([
            'flow' => RequestFlow::Incoming,
            'vendor' => 'vendor',
            'method' => 'POST',
            'headers' => ['Content-Type' => 'application/json'],
            'base_uri' => 'https://echo.hoppscotch.io',
            'path' => '/path',
            'query_parameters' => ['test' => 1],
            'body' => [Str::random(8) => Str::random(32)],
            'request_identifier' => Str::slug(Str::random(12)),
        ]);
        $requestLog->created_at = $createdAt;
        $requestLog->save(['timestamps' => false]);

        ResponseLog::create([
            'request_log_id' => $requestLog->id,
            'success' => true,
            'status_code' => 200,
            'reason_phrase' => 'OK',
            'headers' => ['Content-Type' => 'application/json'],
            'body' => ['hello' => 'world'],
        ]);

        return $requestLog;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestLog = $this->storeRequestLog(Carbon::now()->subDays(35));

        $this->storeRequestLog(Carbon::now()->subDays(5));
    }

    public function test_it_relates_to_response(): void
    {
        $this->assertCount(1, $this->requestLog->responses);
    }

    public function test_it_selects_correct_models_for_prune_by_default(): void
    {
        $this->assertSame(1, (new RequestLog())->prunable()->count());
    }

    public function test_it_selects_corrects_module_for_custom_retention(): void
    {
        config(['request-response-log.prune_after_days' => 2]);

        $this->assertSame(2, (new RequestLog())->prunable()->count());
    }

    public function test_it_selects_no_module_for_unlimited_retention(): void
    {
        config(['request-response-log.prune_after_days' => null]);

        $this->assertSame(0, (new RequestLog())->prunable()->count());
    }
}
