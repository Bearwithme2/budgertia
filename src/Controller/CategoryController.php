<?php

declare(strict_types=1);

namespace App\Controller;

use App\Assembler\CategoryAssembler;
use App\Dto\Request\CategoryCreateRequest;
use App\Http\ApiResponseFactory;
use App\Service\CategoryService;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/categories')]
final class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryService $service,
        private CategoryAssembler $assembler,
        private ApiResponseFactory $responseFactory,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $categories = $this->service->list();
        $data = array_map([$this->assembler, 'toDto'], $categories);

        return $this->responseFactory->create($data);
    }

    #[Route('', methods: ['POST'])]
    public function create(CategoryCreateRequest $request): JsonResponse
    {
        $category = $this->service->create($request);
        $dto = $this->assembler->toDto($category);

        return $this->responseFactory->create($dto, 201);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Category $category): JsonResponse
    {
        return $this->responseFactory->create($this->assembler->toDto($category));
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(CategoryCreateRequest $request, Category $category): JsonResponse
    {
        $category->setName($request->getName());
        $this->service->flush();

        return $this->responseFactory->create($this->assembler->toDto($category));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Category $category): JsonResponse
    {
        $this->service->delete($category);

        return $this->responseFactory->create([], 204);
    }
}
