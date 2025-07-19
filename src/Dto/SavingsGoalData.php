<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class SavingsGoalData
{
    #[Assert\GreaterThan(0)]
    public int $targetAmount;

    #[Assert\GreaterThanOrEqual(0)]
    public int $currentAmount;

    public function __construct(int $targetAmount = 0, int $currentAmount = 0)
    {
        $this->targetAmount = $targetAmount;
        $this->currentAmount = $currentAmount;
    }
}
