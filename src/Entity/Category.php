<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $name;

    /** @var Collection<int, Transaction> */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Transaction::class)]
    private Collection $transactions;

    /** @var Collection<int, BudgetLimit> */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: BudgetLimit::class)]
    private Collection $budgetLimits;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->budgetLimits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    /**
     * @return Collection<int, BudgetLimit>
     */
    public function getBudgetLimits(): Collection
    {
        return $this->budgetLimits;
    }
}
