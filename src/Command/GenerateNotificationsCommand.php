<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Notification;
use App\Entity\SavingsGoal;
use App\Entity\User;
use App\Service\BudgetCalculatorInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:generate-notifications')]
final class GenerateNotificationsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BudgetCalculatorInterface $calculator
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $month = new DateTimeImmutable('first day of this month');
        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            if (!$user instanceof User) {
                continue;
            }

            $this->calculator->checkMonthlyLimits($user, $month);

            $goals = $this->entityManager->getRepository(SavingsGoal::class)
                ->findBy(['user' => $user]);

            foreach ($goals as $goal) {
                if ($goal->getCurrentAmount() >= $goal->getTargetAmount()) {
                    $notification = new Notification();
                    $notification->setUser($user);
                    $notification->setMessage('Savings goal met');
                    $notification->setLevel('info');
                    $notification->setIsRead(false);
                    $notification->setCreatedAt(new DateTimeImmutable());
                    $this->entityManager->persist($notification);
                }
            }
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
