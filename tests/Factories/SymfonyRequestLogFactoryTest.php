<?php

namespace Goedemiddag\RequestResponseLog\Tests\Factories;

use Goedemiddag\RequestResponseLog\Enums\RequestFlow;
use Goedemiddag\RequestResponseLog\Factories\SymfonyRequestLogFactory;
use Goedemiddag\RequestResponseLog\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class SymfonyRequestLogFactoryTest extends TestCase
{
    public function test_it_can_build_a_request_log(): void
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

        $this->assertDatabaseHas('request_logs', [
            'id' => $requestLog->id,
            'flow' => 'in',
            'vendor' => 'test',
            'method' => 'POST',
            'headers' => '{"host":["echo.hoppscotch.io"],"user-agent":["Symfony"],"accept":["application\\/json"],"accept-language":["en-us,en;q=0.5"],"accept-charset":["ISO-8859-1,utf-8;q=0.7,*;q=0.7"],"content-type":["application\\/x-www-form-urlencoded"]}',
            'base_uri' => 'https://echo.hoppscotch.io',
            'path' => '/path',
            'query_parameters' => json_encode(['test' => '1']),
            'body' => json_encode(['hello' => 'world']),
            'request_identifier' => 'hello-world',
        ]);
    }
}
