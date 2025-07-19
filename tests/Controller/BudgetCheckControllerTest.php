<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\BudgetLimit;
use App\Entity\Category;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class BudgetCheckControllerTest extends WebTestCase
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

    public function testCheckEndpoint(): void
    {
        $user = new User();
        $user->setEmail('t@example.com');
        $user->setPassword('x');
        $this->entityManager->persist($user);

        $category = new Category();
        $category->setName('Food');
        $this->entityManager->persist($category);

        $limit = new BudgetLimit();
        $limit->setUser($user);
        $limit->setCategory($category);
        $limit->setAmount(100);
        $this->entityManager->persist($limit);

        $transaction = new Transaction();
        $transaction->setUser($user);
        $transaction->setCategory($category);
        $transaction->setAmount(50);
        $transaction->setDescription('meal');
        $transaction->setDate(new \DateTimeImmutable('2025-01-05'));
        $this->entityManager->persist($transaction);

        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->request('GET', '/api/budget-check?month=2025-01');

        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $this->assertIsString($content);
        $data = json_decode($content, true);
        \assert(is_array($data));
        $this->assertSame(1, count($data['data']));
        $this->assertSame(50, $data['data'][0]['spent']);
    }
}
