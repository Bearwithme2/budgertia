<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\Category;
use App\Entity\User;
use App\Dto\TransactionData;
use App\Service\JsonApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/transactions')]
class TransactionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JsonApi $jsonApi,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $transactions = $this->entityManager->getRepository(Transaction::class)
            ->findBy(['user' => $user]);

        $items = [];
        foreach ($transactions as $transaction) {
            $items[] = [
                'type' => 'transaction',
                'id' => (string) $transaction->getId(),
                'attributes' => [
                    'amount' => $transaction->getAmount(),
                    'description' => $transaction->getDescription(),
                    'date' => $transaction->getDate()->format(DATE_ATOM),
                    'category' => $transaction->getCategory()?->getId(),
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
        $dto = new TransactionData(
            (int) ($payload['amount'] ?? 0),
            (string) ($payload['description'] ?? ''),
            isset($payload['date']) ? new \DateTimeImmutable($payload['date']) : null,
            isset($payload['category']) ? (int) $payload['category'] : null
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse(['message' => 'Invalid data'], 400);
        }

        $category = null;
        if ($dto->category) {
            $category = $this->entityManager->find(Category::class, $dto->category);
            if (!$category) {
                return new JsonResponse(['errors' => [['detail' => 'Category not found']]], 404);
            }
        }

        /** @var User $user */
        $user = $this->getUser();

        $transaction = new Transaction();
        $transaction->setAmount($dto->amount);
        $transaction->setDescription($dto->description);
        $transaction->setDate($dto->date);
        $transaction->setUser($user);
        $transaction->setCategory($category);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        $id = $transaction->getId();
        \assert(is_int($id));

        return new JsonResponse(
            $this->jsonApi->item('transaction', $id, [
                'amount' => $transaction->getAmount(),
                'description' => $transaction->getDescription(),
                'date' => $transaction->getDate()->format(DATE_ATOM),
                'category' => $transaction->getCategory()?->getId(),
            ]),
            201
        );
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Transaction $transaction): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($transaction->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['errors' => [['detail' => 'Not found']]], 404);
        }

        $id = $transaction->getId();
        \assert(is_int($id));

        return new JsonResponse(
            $this->jsonApi->item('transaction', $id, [
                'amount' => $transaction->getAmount(),
                'description' => $transaction->getDescription(),
                'date' => $transaction->getDate()->format(DATE_ATOM),
                'category' => $transaction->getCategory()?->getId(),
            ])
        );
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($transaction->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['errors' => [['detail' => 'Not found']]], 404);
        }

        $data = json_decode($request->getContent(), true);
        $payload = is_array($data) ? $data : [];

        $categoryField = array_key_exists('category', $payload)
            ? (isset($payload['category']) ? (int) $payload['category'] : null)
            : $transaction->getCategory()?->getId();

        $dto = new TransactionData(
            isset($payload['amount']) ? (int) $payload['amount'] : $transaction->getAmount(),
            isset($payload['description']) ? (string) $payload['description'] : $transaction->getDescription(),
            isset($payload['date']) ? new \DateTimeImmutable($payload['date']) : $transaction->getDate(),
            $categoryField
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse(['message' => 'Invalid data'], 400);
        }

        $category = null;
        if ($dto->category) {
            $category = $this->entityManager->find(Category::class, $dto->category);
            if (!$category) {
                return new JsonResponse(['errors' => [['detail' => 'Category not found']]], 404);
            }
        }

        $transaction->setAmount($dto->amount);
        $transaction->setDescription($dto->description);
        $transaction->setDate($dto->date);
        $transaction->setCategory($category);

        $this->entityManager->flush();

        $id = $transaction->getId();
        \assert(is_int($id));

        return new JsonResponse(
            $this->jsonApi->item('transaction', $id, [
                'amount' => $transaction->getAmount(),
                'description' => $transaction->getDescription(),
                'date' => $transaction->getDate()->format(DATE_ATOM),
                'category' => $transaction->getCategory()?->getId(),
            ])
        );
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Transaction $transaction): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($transaction->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['errors' => [['detail' => 'Not found']]], 404);
        }

        $this->entityManager->remove($transaction);
        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }
}
