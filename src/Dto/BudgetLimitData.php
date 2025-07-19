<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class BudgetLimitData
{
    #[Assert\GreaterThan(0)]
    public int $amount;

    #[Assert\Positive]
    public int $category;

    public function __construct(int $amount = 0, int $category = 0)
    {
        $this->amount = $amount;
        $this->category = $category;
    }
}
