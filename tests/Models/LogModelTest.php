<?php

namespace Goedemiddag\RequestResponseLog\Tests\Models;

use Goedemiddag\RequestResponseLog\Models\RequestLog;
use Goedemiddag\RequestResponseLog\Models\ResponseLog;
use Goedemiddag\RequestResponseLog\Tests\TestCase;
use RuntimeException;

class LogModelTest extends TestCase
{
    public function test_it_uses_the_connection_from_the_config(): void
    {
        config()->set('request-response-log.database.connection', 'test');

        $model = new RequestLog();

        $this->assertSame('test', $model->getConnectionName());
    }

    public function test_it_validates_the_connection_from_the_config(): void
    {
        config()->set('request-response-log.database.connection');

        $this->expectException(RuntimeException::class);

        new RequestLog();
    }

    public function test_it_uses_the_request_table_from_the_config(): void
    {
        config()->set('request-response-log.database.request_log_table', 'test_table');

        $model = new RequestLog();

        $this->assertSame('test_table', $model->getTable());
    }

    public function test_it_validates_the_request_table_from_the_config(): void
    {
        config()->set('request-response-log.database.request_log_table');

        $this->expectException(RuntimeException::class);

        new RequestLog();
    }

    public function test_it_uses_the_response_table_from_the_config(): void
    {
        config()->set('request-response-log.database.response_log_table', 'test_table');

        $model = new ResponseLog();

        $this->assertSame('test_table', $model->getTable());
    }

    public function test_it_validates_the_response_table_from_the_config(): void
    {
        config()->set('request-response-log.database.response_log_table');

        $this->expectException(RuntimeException::class);

        new ResponseLog();
    }
}
