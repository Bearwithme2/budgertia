<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Dto\Request\CategoryCreateRequest;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CategoryServiceTest extends KernelTestCase
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

    public function testCreatePersistsCategory(): void
    {
        $service = new CategoryService($this->entityManager);
        $category = $service->create(new CategoryCreateRequest('Test'));

        $this->assertSame('Test', $category->getName());
        $this->assertCount(1, $service->list());
    }

    public function testDeleteRemovesCategory(): void
    {
        $service = new CategoryService($this->entityManager);
        $category = $service->create(new CategoryCreateRequest('Del'));
        $service->delete($category);

        $this->assertCount(0, $service->list());
    }
}
