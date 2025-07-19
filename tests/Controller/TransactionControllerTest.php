<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransactionControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
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
        $container = $this->client->getContainer();
        $em = $container->get(EntityManagerInterface::class);
        \assert($em instanceof EntityManagerInterface);
        $this->entityManager = $em;
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testCreateInvalidData(): void
    {
        $user = new User();
        $user->setEmail('t@example.com');
        $user->setPassword('x');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->request(
            'POST',
            '/api/transactions',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['amount' => 0, 'description' => 'bad', 'date' => '2999-01-01']) ?: ''
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateValidData(): void
    {
        $user = new User();
        $user->setEmail('t@example.com');
        $user->setPassword('x');
        $this->entityManager->persist($user);

        $category = new Category();
        $category->setName('Food');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->request(
            'POST',
            '/api/transactions',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'amount' => 5,
                'description' => 'ok',
                'date' => '2000-01-01',
                'category' => $category->getId(),
            ]) ?: ''
        );

        $this->assertResponseStatusCodeSame(201);
    }
}
