<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Service\JsonApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/categories')]
class CategoryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JsonApi $jsonApi
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        $items = [];
        foreach ($categories as $category) {
            $items[] = [
                'type' => 'category',
                'id' => (string) $category->getId(),
                'attributes' => [
                    'name' => $category->getName(),
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
        $name = isset($payload['name']) ? (string) $payload['name'] : '';

        $category = new Category();
        $category->setName($name);
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $id = $category->getId();
        \assert(is_int($id));

        return new JsonResponse(
            $this->jsonApi->item('category', $id, ['name' => $category->getName()]),
            201
        );
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Category $category): JsonResponse
    {
        $id = $category->getId();
        \assert(is_int($id));

        return new JsonResponse(
            $this->jsonApi->item('category', $id, ['name' => $category->getName()])
        );
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Category $category): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $payload = is_array($data) ? $data : [];
        if (isset($payload['name'])) {
            $category->setName((string) $payload['name']);
        }
        $this->entityManager->flush();

        $id = $category->getId();
        \assert(is_int($id));

        return new JsonResponse(
            $this->jsonApi->item('category', $id, ['name' => $category->getName()])
        );
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Category $category): JsonResponse
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }
}
