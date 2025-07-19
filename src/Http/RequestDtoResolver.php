<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class RequestDtoResolver implements ValueResolverInterface
{
    /**
     * @return iterable<object>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();
        if (!$type || !str_starts_with($type, 'App\\Dto\\Request\\')) {
            return [];
        }

        $data = json_decode($request->getContent(), true) ?: [];
        yield new $type(...array_values((array) $data));
    }
}
