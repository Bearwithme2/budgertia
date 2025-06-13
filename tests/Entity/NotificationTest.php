<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Notification;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    public function testAccessors(): void
    {
        $user = new User();
        $notification = new Notification();
        $notification->setMessage('Alert');
        $notification->setLevel('warning');
        $notification->setIsRead(true);
        $date = new \DateTimeImmutable('2025-01-01');
        $notification->setCreatedAt($date);
        $notification->setUser($user);

        $this->assertSame('Alert', $notification->getMessage());
        $this->assertSame('warning', $notification->getLevel());
        $this->assertTrue($notification->isRead());
        $this->assertSame($date, $notification->getCreatedAt());
        $this->assertSame($user, $notification->getUser());
    }
}
