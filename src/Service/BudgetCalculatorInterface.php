<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;

interface BudgetCalculatorInterface
{
    /**
     * @return array<int, array{category: int, spent: int, limit: int, over: bool}>
     */
    public function checkMonthlyLimits(User $user, \DateTimeImmutable $month): array;
}
