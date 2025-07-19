<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\ORM\Tools\SchemaTool;

class AuthControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        if (!extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('pdo_sqlite missing');
        }
        $this->client = static::createClient();
        $this->client->disableReboot();
        $container = $this->client->getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        \assert($entityManager instanceof EntityManagerInterface);
        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testRegisterEndpoint(): void
    {
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'john@example.com', 'password' => 'secret123']) ?: ''
        );

        $this->assertResponseStatusCodeSame(201);
        $content = $this->client->getResponse()->getContent();
        $this->assertIsString($content);
        $data = json_decode($content, true);
        \assert(is_array($data));
        $this->assertArrayHasKey('token', $data);
    }

    public function testLoginEndpoint(): void
    {
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'login@example.com', 'password' => 'secret123']) ?: ''
        );
        $this->assertResponseStatusCodeSame(201);

        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'login@example.com', 'password' => 'secret123']) ?: ''
        );

        $this->assertResponseStatusCodeSame(200);
        $content = $this->client->getResponse()->getContent();
        \assert(is_string($content));
        $data = json_decode($content, true);
        \assert(is_array($data));
        $this->assertArrayHasKey('token', $data);
    }
}
