<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BudgetLimitControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private JWTTokenManagerInterface $jwt;
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
        $em = $container->get(EntityManagerInterface::class);
        \assert($em instanceof EntityManagerInterface);
        $this->entityManager = $em;
        $jwt = $container->get(JWTTokenManagerInterface::class);
        \assert($jwt instanceof JWTTokenManagerInterface);
        $this->jwt = $jwt;
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testCreateAndList(): void
    {
        $user = new User();
        $user->setEmail('limit@example.com');
        $user->setPassword('x');
        $this->entityManager->persist($user);

        $category = new Category();
        $category->setName('Food');
        $this->entityManager->persist($category);

        $this->entityManager->flush();

        $token = $this->jwt->create($user);
        $this->client->request(
            'POST',
            '/api/budget-limits',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => 'Bearer ' . $token,
            ],
            json_encode(['amount' => 100, 'category' => $category->getId()]) ?: ''
        );

        $this->assertResponseStatusCodeSame(201);

        $token = $this->jwt->create($user);
        $this->client->request('GET', '/api/budget-limits', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);
        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        \assert(is_string($content));
        $data = json_decode($content, true);
        \assert(is_array($data));
        $this->assertCount(1, $data['data']);
    }
}
