<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\JsonApi;
use PHPUnit\Framework\TestCase;

final class JsonApiTest extends TestCase
{
    public function testItemAndCollection(): void
    {
        $api = new JsonApi();
        $item = $api->item('thing', 1, ['foo' => 'bar']);
        $this->assertSame('thing', $item['data']['type']);
        $this->assertSame('1', $item['data']['id']);
        $this->assertSame(['foo' => 'bar'], $item['data']['attributes']);

        $collection = $api->collection([$item['data']]);
        $this->assertCount(1, $collection['data']);
        $this->assertSame('thing', $collection['data'][0]['type']);
    }
}
