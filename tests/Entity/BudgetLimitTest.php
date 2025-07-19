<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\BudgetLimit;
use App\Entity\Category;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class BudgetLimitTest extends TestCase
{
    public function testAccessors(): void
    {
        $limit = new BudgetLimit();
        $user = new User();
        $category = new Category();
        $category->setName('Food');

        $limit->setAmount(100);
        $limit->setUser($user);
        $limit->setCategory($category);

        $this->assertSame(100, $limit->getAmount());
        $this->assertSame($user, $limit->getUser());
        $this->assertSame($category, $limit->getCategory());
    }
}
