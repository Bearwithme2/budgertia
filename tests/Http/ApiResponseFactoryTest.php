<?php

declare(strict_types=1);

namespace App\Tests\Http;

use App\Http\ApiResponseFactory;
use PHPUnit\Framework\TestCase;

final class ApiResponseFactoryTest extends TestCase
{
    public function testMetaAndHeader(): void
    {
        $factory = new ApiResponseFactory();
        $response = $factory->create(['foo' => 'bar'], 201);

        $content = $response->getContent();
        \assert(is_string($content));
        $data = json_decode($content, true);
        \assert(is_array($data));

        $this->assertArrayHasKey('meta', $data);
        $this->assertArrayHasKey('requestId', $data['meta']);
        $this->assertSame(['foo' => 'bar'], $data['data']);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame($data['meta']['requestId'], $response->headers->get('X-Request-Id'));
    }
}
