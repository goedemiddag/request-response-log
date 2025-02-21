<?php

namespace Goedemiddag\RequestResponseLog\Tests\Middleware;

use Goedemiddag\RequestResponseLog\Middleware\ApplicationRequestResponseLogger;
use Goedemiddag\RequestResponseLog\Models\RequestLog;
use Goedemiddag\RequestResponseLog\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;

class ApplicationRequestResponseLoggerTest extends TestCase
{
    public function test_middleware(): void
    {
        $request = Request::create(
            uri: '/app-middleware?app=1',
            method: 'POST',
            content: json_encode(['logged' => 'success']),
        );

        Context::add('request-identifier', 'test-request-identifier');

        $next = function () {
            return response('This is the response', 200);
        };

        $middleware = new ApplicationRequestResponseLogger();

        $response = $middleware->handle($request, $next, 'application');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas(RequestLog::class, [
            'flow' => 'in',
            'vendor' => 'application',
            'method' => 'POST',
            'path' => '/app-middleware',
            'query_parameters' => json_encode(['app' => '1']),
            'body' => json_encode(['logged' => 'success']),
            'request_identifier' => 'test-request-identifier',
        ]);
    }
}
