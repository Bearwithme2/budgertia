<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testAccessors(): void
    {
        $user = new User();
        $user->setEmail('a@example.com');
        $user->setPassword('secret');
        $user->setRoles(['ROLE_ADMIN']);

        $this->assertNull($user->getId());
        $this->assertSame('a@example.com', $user->getEmail());
        $this->assertSame('secret', $user->getPassword());
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertSame('a@example.com', $user->getUserIdentifier());
    }
}
