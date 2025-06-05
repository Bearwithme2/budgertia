<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column]
    private string $password;

    /** @var Collection<int, Transaction> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Transaction::class)]
    private Collection $transactions;

    /** @var Collection<int, BudgetLimit> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BudgetLimit::class)]
    private Collection $budgetLimits;

    /** @var Collection<int, SavingsGoal> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SavingsGoal::class)]
    private Collection $savingsGoals;

    /** @var Collection<int, Notification> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class)]
    private Collection $notifications;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->budgetLimits = new ArrayCollection();
        $this->savingsGoals = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    /**
     * @return Collection<int, SavingsGoal>
     */
    public function getSavingsGoals(): Collection
    {
        return $this->savingsGoals;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }
}
