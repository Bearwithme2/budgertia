<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BudgetLimit;
use App\Entity\Category;
use App\Entity\User;
use App\Service\JsonApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/budget-limits')]
class BudgetLimitController extends AbstractController
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
        $limits = $this->entityManager->getRepository(BudgetLimit::class)
            ->findBy(['user' => $user]);

        $items = [];
        foreach ($limits as $limit) {
            $items[] = [
                'type' => 'budget-limit',
                'id' => (string) $limit->getId(),
                'attributes' => [
                    'amount' => $limit->getAmount(),
                    'category' => $limit->getCategory()->getId(),
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

        $category = $this->entityManager->find(Category::class, $payload['category'] ?? 0);
        if (!$category) {
            return new JsonResponse(['errors' => [['detail' => 'Category not found']]], 404);
        }

        /** @var User $user */
        $user = $this->getUser();

        $limit = new BudgetLimit();
        $limit->setAmount((int) ($payload['amount'] ?? 0));
        $limit->setCategory($category);
        $limit->setUser($user);

        $this->entityManager->persist($limit);
        $this->entityManager->flush();

        $id = $limit->getId();
        \assert(is_int($id));

        return new JsonResponse(
            $this->jsonApi->item('budget-limit', $id, [
                'amount' => $limit->getAmount(),
                'category' => $limit->getCategory()->getId(),
            ]),
            201
        );
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(BudgetLimit $budgetLimit): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($budgetLimit->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['errors' => [['detail' => 'Not found']]], 404);
        }

        $id = $budgetLimit->getId();
        \assert(is_int($id));

        return new JsonResponse(
            $this->jsonApi->item('budget-limit', $id, [
                'amount' => $budgetLimit->getAmount(),
                'category' => $budgetLimit->getCategory()->getId(),
            ])
        );
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, BudgetLimit $budgetLimit): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($budgetLimit->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['errors' => [['detail' => 'Not found']]], 404);
        }

        $data = json_decode($request->getContent(), true);
        $payload = is_array($data) ? $data : [];
        if (isset($payload['amount'])) {
            $budgetLimit->setAmount((int) $payload['amount']);
        }
        if (isset($payload['category'])) {
            $category = $this->entityManager->find(Category::class, $payload['category']);
            if (!$category) {
                return new JsonResponse(['errors' => [['detail' => 'Category not found']]], 404);
            }
            $budgetLimit->setCategory($category);
        }

        $this->entityManager->flush();

        $id = $budgetLimit->getId();
        \assert(is_int($id));

        return new JsonResponse(
            $this->jsonApi->item('budget-limit', $id, [
                'amount' => $budgetLimit->getAmount(),
                'category' => $budgetLimit->getCategory()->getId(),
            ])
        );
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(BudgetLimit $budgetLimit): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($budgetLimit->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['errors' => [['detail' => 'Not found']]], 404);
        }

        $this->entityManager->remove($budgetLimit);
        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }
}
