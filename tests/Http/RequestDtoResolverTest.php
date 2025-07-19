<?php

declare(strict_types=1);

namespace App\Tests\Http;

use App\Dto\Request\CategoryCreateRequest;
use App\Http\RequestDtoResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class RequestDtoResolverTest extends TestCase
{
    public function testResolvesDto(): void
    {
        $resolver = new RequestDtoResolver();
        $request = new Request([], [], [], [], [], [], (string) json_encode(['name' => 'X']));
        $arg = new ArgumentMetadata('dto', CategoryCreateRequest::class, false, false, null);

        $result = $resolver->resolve($request, $arg);
        $dto = current(iterator_to_array($result));

        $this->assertInstanceOf(CategoryCreateRequest::class, $dto);
        $this->assertSame('X', $dto->getName());
    }

    public function testIgnoresNonDto(): void
    {
        $resolver = new RequestDtoResolver();
        $request = new Request();
        $arg = new ArgumentMetadata('req', 'string', false, false, null);

        $this->assertSame([], iterator_to_array($resolver->resolve($request, $arg)));
    }
}
