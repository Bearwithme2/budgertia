<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\BudgetCalculatorInterface;
use App\Service\JsonApi;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/budget-check')]
final class BudgetCheckController extends AbstractController
{
    public function __construct(
        private BudgetCalculatorInterface $calculator,
        private JsonApi $jsonApi
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function check(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $monthParam = (string) $request->query->get('month');
        $month = $monthParam !== ''
            ? new DateTimeImmutable($monthParam . '-01')
            : new DateTimeImmutable('first day of this month');

        $results = $this->calculator->checkMonthlyLimits($user, $month);

        return new JsonResponse(['data' => $results]);
    }
}
