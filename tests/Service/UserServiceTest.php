<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\Tools\SchemaTool;

class UserServiceTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('pdo_sqlite missing');
        }
        self::bootKernel();
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        \assert($entityManager instanceof EntityManagerInterface);
        \assert($passwordHasher instanceof UserPasswordHasherInterface);
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testRegisterCreatesUser(): void
    {
        $service = new UserService($this->entityManager, $this->passwordHasher);
        $user = $service->register('a@example.com', 'secret');

        $this->assertSame('a@example.com', $user->getEmail());
        $this->assertTrue($this->passwordHasher->isPasswordValid($user, 'secret'));
    }

    public function testRegisterDuplicateThrows(): void
    {
        $service = new UserService($this->entityManager, $this->passwordHasher);
        $service->register('a@example.com', 'secret');

        $this->expectException(\RuntimeException::class);
        $service->register('a@example.com', 'secret');
    }
}
