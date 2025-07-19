<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class ProdComposeTest extends TestCase
{
    public function testCronServiceExists(): void
    {
        /** @var array<string, mixed> $data */
        $data = Yaml::parseFile(__DIR__ . '/../../docker-compose.prod.yml');
        $this->assertIsArray($data);
        $this->assertArrayHasKey('services', $data);
        $services = $data['services'];
        $this->assertIsArray($services);
        $this->assertArrayHasKey('cron', $services);
        $this->assertArrayHasKey('volumes', $data);
        $volumes = $data['volumes'];
        $this->assertIsArray($volumes);
        $this->assertArrayHasKey('sqlite-data', $volumes);
    }
}
