<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SavingsGoal;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class SavingsGoalTest extends TestCase
{
    public function testAccessors(): void
    {
        $goal = new SavingsGoal();
        $user = new User();

        $goal->setTargetAmount(1000);
        $goal->setCurrentAmount(100);
        $goal->setUser($user);

        $this->assertSame(1000, $goal->getTargetAmount());
        $this->assertSame(100, $goal->getCurrentAmount());
        $this->assertSame($user, $goal->getUser());
    }
}
