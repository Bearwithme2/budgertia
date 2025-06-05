<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\SavingsGoal;
use App\Entity\User;
use App\Service\JsonApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/savings-goals')]
class SavingsGoalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JsonApi $jsonApi
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $goals = $this->entityManager->getRepository(SavingsGoal::class)
            ->findBy(['user' => $user]);

        $items = [];
        foreach ($goals as $goal) {
            $items[] = [
                'type' => 'savings-goal',
                'id' => (string) $goal->getId(),
                'attributes' => [
                    'targetAmount' => $goal->getTargetAmount(),
                    'currentAmount' => $goal->getCurrentAmount(),
                ],
            ];
        }

        return new JsonResponse($this->jsonApi->collection($items));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $payload = is_array($data) ? $data : [];

        /** @var User $user */
        $user = $this->getUser();

        $goal = new SavingsGoal();
        $goal->setTargetAmount((int) ($payload['targetAmount'] ?? 0));
        $goal->setCurrentAmount((int) ($payload['currentAmount'] ?? 0));
        $goal->setUser($user);

        $this->entityManager->persist($goal);
        $this->entityManager->flush();

        $id = $goal->getId();
        \assert(is_int($id));

        return new JsonResponse(
            $this->jsonApi->item('savings-goal', $id, [
                'targetAmount' => $goal->getTargetAmount(),
                'currentAmount' => $goal->getCurrentAmount(),
            ]),
            201
        );
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(SavingsGoal $savingsGoal): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($savingsGoal->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['errors' => [['detail' => 'Not found']]], 404);
        }

        $id = $savingsGoal->getId();
        \assert(is_int($id));

        return new JsonResponse(
            $this->jsonApi->item('savings-goal', $id, [
                'targetAmount' => $savingsGoal->getTargetAmount(),
                'currentAmount' => $savingsGoal->getCurrentAmount(),
            ])
        );
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, SavingsGoal $savingsGoal): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($savingsGoal->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['errors' => [['detail' => 'Not found']]], 404);
        }

        $data = json_decode($request->getContent(), true);
        $payload = is_array($data) ? $data : [];
        if (isset($payload['targetAmount'])) {
            $savingsGoal->setTargetAmount((int) $payload['targetAmount']);
        }
        if (isset($payload['currentAmount'])) {
            $savingsGoal->setCurrentAmount((int) $payload['currentAmount']);
        }

        $this->entityManager->flush();

        $id = $savingsGoal->getId();
        \assert(is_int($id));

        return new JsonResponse(
            $this->jsonApi->item('savings-goal', $id, [
                'targetAmount' => $savingsGoal->getTargetAmount(),
                'currentAmount' => $savingsGoal->getCurrentAmount(),
            ])
        );
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(SavingsGoal $savingsGoal): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($savingsGoal->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['errors' => [['detail' => 'Not found']]], 404);
        }

        $this->entityManager->remove($savingsGoal);
        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }
}
