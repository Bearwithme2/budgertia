<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TesterControllerTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    protected function setUp(): void
    {
        if (!class_exists(\Symfony\Component\BrowserKit\AbstractBrowser::class)) {
            self::markTestSkipped('BrowserKit missing');
        }
    }

    public function testTesterPageLoads(): void
    {
        /** @var KernelBrowser $client */
        $client = static::createClient();
        /* @phpstan-ignore-next-line */
        $client->request('GET', '/tester');

        $this->assertResponseIsSuccessful();
        /* @phpstan-ignore-next-line */
        $this->assertStringContainsString('id="tester"', $client->getResponse()->getContent());
    }
}
