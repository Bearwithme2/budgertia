<?php

declare(strict_types=1);

namespace App\Tests\Assembler;

use App\Assembler\CategoryAssembler;
use App\Dto\Response\CategoryResponse;
use App\Entity\Category;
use PHPUnit\Framework\TestCase;

final class CategoryAssemblerTest extends TestCase
{
    public function testToDtoMapsFields(): void
    {
        $category = new Category();
        $category->setName('Food');
        $ref = new \ReflectionProperty($category, 'id');
        $ref->setAccessible(true);
        $ref->setValue($category, 5);

        $dto = (new CategoryAssembler())->toDto($category);

        $this->assertInstanceOf(CategoryResponse::class, $dto);
        $this->assertSame(5, $dto->getId());
        $this->assertSame('Food', $dto->getName());
    }
}
