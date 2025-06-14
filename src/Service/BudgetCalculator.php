<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\BudgetLimit;
use App\Entity\Notification;
use App\Entity\Transaction;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class BudgetCalculator implements BudgetCalculatorInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return array<int, array{category: int, spent: int, limit: int, over: bool}>
     */
    public function checkMonthlyLimits(User $user, DateTimeImmutable $month): array
    {
        $start = $month->modify('first day of this month')->setTime(0, 0);
        $end = $start->modify('last day of this month')->setTime(23, 59, 59);

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('IDENTITY(t.category) AS category', 'SUM(t.amount) AS spent')
            ->from(Transaction::class, 't')
            ->where('t.user = :user')
            ->andWhere('t.date BETWEEN :start AND :end')
            ->groupBy('category')
            ->setParameters([
                'user' => $user,
                'start' => $start,
                'end' => $end,
            ]);

        $spentData = [];
        foreach ($qb->getQuery()->getArrayResult() as $row) {
            $catId = (int) $row['category'];
            $spentData[$catId] = (int) $row['spent'];
        }

        $results = [];
        $limits = $this->entityManager->getRepository(BudgetLimit::class)
            ->findBy(['user' => $user]);

        foreach ($limits as $limit) {
            $category = $limit->getCategory();
            $catId = $category->getId();
            if (!is_int($catId)) {
                continue;
            }
            $spent = $spentData[$catId] ?? 0;
            $over = $spent > $limit->getAmount();
            $results[] = [
                'category' => $catId,
                'spent' => $spent,
                'limit' => $limit->getAmount(),
                'over' => $over,
            ];

            if ($over) {
                $notification = new Notification();
                $notification->setUser($user);
                $notification->setMessage('Budget limit exceeded for ' . $category->getName());
                $notification->setLevel('warning');
                $notification->setIsRead(false);
                $notification->setCreatedAt(new DateTimeImmutable());
                $this->entityManager->persist($notification);
            }
        }

        $this->entityManager->flush();

        return $results;
    }
}
