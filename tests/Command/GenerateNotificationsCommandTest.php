<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Entity\BudgetLimit;
use App\Entity\Category;
use App\Entity\Notification;
use App\Entity\SavingsGoal;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

class GenerateNotificationsCommandTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private Application $application;

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
        \assert(self::$kernel instanceof KernelInterface);
        $this->application = new Application(self::$kernel);
    }

    public function testNotificationsAreCreated(): void
    {
        $user = new User();
        $user->setEmail('cmd@example.com');
        $user->setPassword('x');
        $this->entityManager->persist($user);

        $category = new Category();
        $category->setName('Misc');
        $this->entityManager->persist($category);

        $limit = new BudgetLimit();
        $limit->setUser($user);
        $limit->setCategory($category);
        $limit->setAmount(100);
        $this->entityManager->persist($limit);

        $transaction = new Transaction();
        $transaction->setUser($user);
        $transaction->setCategory($category);
        $transaction->setAmount(150);
        $transaction->setDescription('big');
        $transaction->setDate(new \DateTimeImmutable('first day of this month'));
        $this->entityManager->persist($transaction);

        $goal = new SavingsGoal();
        $goal->setUser($user);
        $goal->setTargetAmount(200);
        $goal->setCurrentAmount(200);
        $this->entityManager->persist($goal);

        $this->entityManager->flush();

        $command = $this->application->find('app:generate-notifications');
        $tester = new CommandTester($command);
        $exitCode = $tester->execute([]);
        $this->assertSame(0, $exitCode);

        $notifications = $this->entityManager->getRepository(Notification::class)->findAll();
        $this->assertCount(2, $notifications);
    }
}
