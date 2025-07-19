<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

final class CategoryTest extends TestCase
{
    public function testAccessors(): void
    {
        $category = new Category();
        $category->setName('Food');

        $this->assertNull($category->getId());
        $this->assertSame('Food', $category->getName());
        $this->assertCount(0, $category->getTransactions());
        $this->assertCount(0, $category->getBudgetLimits());
    }
}
