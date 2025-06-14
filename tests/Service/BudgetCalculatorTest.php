<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\BudgetLimit;
use App\Entity\Category;
use App\Entity\Transaction;
use App\Entity\User;
use App\Service\BudgetCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BudgetCalculatorTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    protected function setUp(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('pdo_sqlite missing');
        }
        self::bootKernel();
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        \assert($em instanceof EntityManagerInterface);
        $this->entityManager = $em;
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testCheckMonthlyLimitsCreatesNotification(): void
    {
        $user = new User();
        $user->setEmail('a@example.com');
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

        $t1 = new Transaction();
        $t1->setUser($user);
        $t1->setCategory($category);
        $t1->setAmount(70);
        $t1->setDescription('A');
        $t1->setDate(new \DateTimeImmutable('2025-01-05'));
        $this->entityManager->persist($t1);

        $t2 = new Transaction();
        $t2->setUser($user);
        $t2->setCategory($category);
        $t2->setAmount(50);
        $t2->setDescription('B');
        $t2->setDate(new \DateTimeImmutable('2025-01-10'));
        $this->entityManager->persist($t2);

        $this->entityManager->flush();

        $service = new BudgetCalculator($this->entityManager);
        $result = $service->checkMonthlyLimits($user, new \DateTimeImmutable('2025-01-01'));

        $this->assertTrue($result[0]['over']);
        $notifications = $this->entityManager->getRepository(\App\Entity\Notification::class)->findAll();
        $this->assertCount(1, $notifications);
    }
}
