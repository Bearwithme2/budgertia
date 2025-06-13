<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NotificationRepositoryTest extends KernelTestCase
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
        $entityManager = $container->get(EntityManagerInterface::class);
        \assert($entityManager instanceof EntityManagerInterface);
        $this->entityManager = $entityManager;
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testPersistNotification(): void
    {
        $user = new User();
        $user->setEmail('n@example.com');
        $user->setPassword('x');
        $this->entityManager->persist($user);

        $notification = new Notification();
        $notification->setUser($user);
        $notification->setMessage('Budget exceeded');
        $notification->setLevel('warning');
        $notification->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $repo = $this->entityManager->getRepository(Notification::class);
        $found = $repo->findOneBy(['message' => 'Budget exceeded']);
        $this->assertInstanceOf(Notification::class, $found);
        $this->assertSame('warning', $found->getLevel());
    }
}
