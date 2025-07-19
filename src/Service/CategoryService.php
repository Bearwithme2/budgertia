<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Request\CategoryCreateRequest;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

final class CategoryService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return Category[]
     */
    public function list(): array
    {
        return $this->entityManager->getRepository(Category::class)->findAll();
    }

    public function create(CategoryCreateRequest $request): Category
    {
        $category = new Category();
        $category->setName($request->getName());
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }

    public function delete(Category $category): void
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }
}
