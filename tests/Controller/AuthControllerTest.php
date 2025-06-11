<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use Doctrine\ORM\Tools\SchemaTool;

class AuthControllerTest extends WebTestCase
{
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
        self::bootKernel();
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        \assert($entityManager instanceof EntityManagerInterface);
        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testRegisterEndpoint(): void
    {
        /** @var HttpKernelBrowser $client */
        $client = static::createClient();
        /* @phpstan-ignore-next-line */
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'john@example.com', 'password' => 'secret'])
        );

        $this->assertResponseStatusCodeSame(201);
        /* @phpstan-ignore-next-line */
        $data = json_decode($client->getResponse()->getContent(), true);
        \assert(is_array($data));
        $this->assertArrayHasKey('token', $data);
    }
}
