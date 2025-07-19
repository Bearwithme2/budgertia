<?php

declare(strict_types=1);

namespace App\Dto;

use App\Validator\Constraints\NoFutureDate;
use Symfony\Component\Validator\Constraints as Assert;

final class TransactionData
{
    #[Assert\GreaterThan(0)]
    public int $amount;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $description;

    #[Assert\NotNull]
    #[NoFutureDate]
    public \DateTimeImmutable $date;

    #[Assert\Positive]
    public ?int $category;

    public function __construct(
        int $amount = 0,
        string $description = '',
        ?\DateTimeImmutable $date = null,
        ?int $category = null
    ) {
        $this->amount = $amount;
        $this->description = $description;
        $this->date = $date ?? new \DateTimeImmutable();
        $this->category = $category;
    }
}
