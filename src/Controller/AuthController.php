<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Dto\UserRegistrationData;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserService $userService,
        JWTManager $jwtManager,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $payload = is_array($data) ? $data : [];
        $dto = new UserRegistrationData(
            (string) ($payload['email'] ?? ''),
            (string) ($payload['password'] ?? '')
        );

        $violations = $validator->validate($dto);
        if (count($violations) > 0) {
            return new JsonResponse(['message' => 'Invalid data'], 400);
        }
        try {
            $user = $userService->register($dto->email, $dto->password);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }

        return new JsonResponse(['token' => $jwtManager->create($user)], 201);
    }
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        JWTManager $jwtManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $payload = is_array($data) ? $data : [];
        $email = $payload['email'] ?? '';
        $password = $payload['password'] ?? '';

        /** @var User|null $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['message' => 'Invalid credentials'], 401);
        }

        return new JsonResponse(['token' => $jwtManager->create($user)]);
    }


    #[Route('/api/token/refresh', name: 'api_token_refresh', methods: ['POST'])]
    public function refresh(
        Request $request,
        EntityManagerInterface $entityManager,
        JWTManager $jwtManager
    ): JsonResponse {
        $auth = $request->headers->get('Authorization');
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            return new JsonResponse(['message' => 'Token missing'], 400);
        }

        $oldToken = substr($auth, 7);

        try {
            $payload = $jwtManager->parse($oldToken);
        } catch (\Exception) {
            return new JsonResponse(['message' => 'Invalid token'], 400);
        }

        $userId = $payload[$jwtManager->getUserIdClaim()] ?? null;
        if (!$userId) {
            return new JsonResponse(['message' => 'Invalid token'], 400);
        }

        /** @var User|null $user */
        $user = $entityManager->find(User::class, $userId);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }

        return new JsonResponse(['token' => $jwtManager->create($user)]);
    }
}
