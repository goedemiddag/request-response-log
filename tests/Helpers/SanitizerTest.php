<?php

namespace Goedemiddag\RequestResponseLog\Tests\Helpers;

use Goedemiddag\RequestResponseLog\Helpers\Sanitizer;
use Goedemiddag\RequestResponseLog\Tests\TestCase;

class SanitizerTest extends TestCase
{
    public function test_it_filters_sensitive_data(): void
    {
        $data = [
            'password' => 'secret',
            'hello' => 'world',
        ];

        $sanitizedData = Sanitizer::filterSensitiveData($data);

        $this->assertArrayHasKey('password', $sanitizedData);
        $this->assertArrayHasKey('hello', $sanitizedData);
        $this->assertSame('********', $sanitizedData['password']);
        $this->assertSame('world', $sanitizedData['hello']);
    }

    public function test_it_filters_sensitive_data_vendor(): void
    {
        config()->set('request-response-log.security.sensitive_fields_per_vendor.test-vendor', ['hello']);

        $data = [
            'password' => 'secret',
            'hello' => 'world',
            'foo' => 'bar',
        ];

        $sanitizedData = Sanitizer::filterSensitiveData($data, 'test-vendor');

        $this->assertArrayHasKey('password', $sanitizedData);
        $this->assertArrayHasKey('hello', $sanitizedData);
        $this->assertArrayHasKey('foo', $sanitizedData);
        $this->assertSame('********', $sanitizedData['password']);
        $this->assertSame('********', $sanitizedData['hello']);
        $this->assertSame('bar', $sanitizedData['foo']);
    }

    public function test_it_filters_sensitive_data_nested(): void
    {
        $data = [
            'foo' => 'bar',
            'nested' => [
                'password' => 'secret2',
                'hello_to' => 'the_world',
            ],
        ];

        $sanitizedData = Sanitizer::filterSensitiveData($data);

        $this->assertArrayHasKey('foo', $sanitizedData);
        $this->assertArrayHasKey('nested', $sanitizedData);
        $this->assertArrayHasKey('password', $sanitizedData['nested']);
        $this->assertArrayHasKey('hello_to', $sanitizedData['nested']);
        $this->assertSame('********', $sanitizedData['nested']['password']);
        $this->assertSame('the_world', $sanitizedData['nested']['hello_to']);
    }

    public function test_it_sanitizes_json(): void
    {
        $body = json_encode([
            'password' => 'secret',
            'hello' => 'world',
        ]);

        $sanitizedBody = Sanitizer::sanitizeBody($body);

        $this->assertIsArray($sanitizedBody);
        $this->assertArrayHasKey('password', $sanitizedBody);
        $this->assertArrayHasKey('hello', $sanitizedBody);
        $this->assertSame('********', $sanitizedBody['password']);
        $this->assertSame('world', $sanitizedBody['hello']);
    }

    public function test_it_ignores_non_json(): void
    {
        $body = '<xml><items><hello>world</hello><password>secret</password></items></xml>';

        $sanitizedBody = Sanitizer::sanitizeBody($body);

        $this->assertIsString($sanitizedBody);
        $this->assertStringContainsString('hello', $sanitizedBody);
        $this->assertStringContainsString('world', $sanitizedBody);
        $this->assertStringContainsString('password', $sanitizedBody);
        $this->assertStringContainsString('secret', $sanitizedBody);
        $this->assertStringNotContainsString('********', $sanitizedBody);
    }

    public function test_it_ignores_json_strings(): void
    {
        $sanitizedData = Sanitizer::sanitizeBody(json_encode('hello'));

        $this->assertIsString($sanitizedData);
        $this->assertSame('hello', $sanitizedData);
    }
}
