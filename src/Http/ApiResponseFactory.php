<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

final class ApiResponseFactory
{
    /**
     * @param mixed $data
     */
    public function create(mixed $data, int $status = 200): JsonResponse
    {
        $requestId = bin2hex(random_bytes(16));
        $payload = [
            'data' => $data,
            'meta' => ['requestId' => $requestId],
        ];

        $response = new JsonResponse($payload, $status);
        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }
}
