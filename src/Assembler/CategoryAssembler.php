<?php

declare(strict_types=1);

namespace App\Assembler;

use App\Dto\Response\CategoryResponse;
use App\Entity\Category;

final class CategoryAssembler
{
    public function toDto(Category $category): CategoryResponse
    {
        $id = $category->getId();
        \assert(is_int($id));

        return new CategoryResponse($id, $category->getName());
    }
}
