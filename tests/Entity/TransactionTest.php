<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Category;
use PHPUnit\Framework\TestCase;

final class TransactionTest extends TestCase
{
    public function testAccessors(): void
    {
        $transaction = new Transaction();
        $user = new User();
        $category = new Category();
        $category->setName('Food');
        $date = new \DateTimeImmutable('2025-01-01');

        $transaction->setAmount(10);
        $transaction->setDescription('Dinner');
        $transaction->setDate($date);
        $transaction->setUser($user);
        $transaction->setCategory($category);

        $this->assertSame(10, $transaction->getAmount());
        $this->assertSame('Dinner', $transaction->getDescription());
        $this->assertSame($date, $transaction->getDate());
        $this->assertSame($user, $transaction->getUser());
        $this->assertSame($category, $transaction->getCategory());
    }
}
