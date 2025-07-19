<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private KernelBrowser $client;

    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        if (!extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('pdo_sqlite missing');
        }
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $em = $container->get(EntityManagerInterface::class);
        \assert($em instanceof EntityManagerInterface);
        $this->entityManager = $em;
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    /**
     * @return array{0: User, 1: Notification, 2: Notification, 3: Notification}
     */
    private function createUserWithNotifications(): array
    {
        $user = new User();
        $user->setEmail('n@example.com');
        $user->setPassword('x');
        $this->entityManager->persist($user);

        $n1 = new Notification();
        $n1->setUser($user);
        $n1->setMessage('A');
        $n1->setLevel('info');
        $n1->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($n1);

        $n2 = new Notification();
        $n2->setUser($user);
        $n2->setMessage('B');
        $n2->setLevel('info');
        $n2->setCreatedAt(new \DateTimeImmutable());
        $n2->setIsRead(true);
        $this->entityManager->persist($n2);

        $n3 = new Notification();
        $n3->setUser($user);
        $n3->setMessage('C');
        $n3->setLevel('warning');
        $n3->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($n3);

        $this->entityManager->flush();

        return [$user, $n1, $n2, $n3];
    }

    public function testList(): void
    {
        [$user] = $this->createUserWithNotifications();

        $this->client->loginUser($user);
        $this->client->request('GET', '/api/notifications');

        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $this->assertIsString($content);
        $data = json_decode($content, true);
        \assert(is_array($data));
        $this->assertCount(2, $data['data']);
    }

    public function testMarkRead(): void
    {
        [$user, $n1] = $this->createUserWithNotifications();

        $this->client->loginUser($user);
        $id = $n1->getId();
        \assert(is_int($id));
        $this->client->request('PATCH', '/api/notifications/' . $id . '/read');

        $this->assertResponseIsSuccessful();
        $this->entityManager->clear();
        $updated = $this->entityManager->find(Notification::class, $id);
        \assert($updated instanceof Notification);
        $this->assertTrue($updated->isRead());
    }

    public function testMarkAllRead(): void
    {
        [$user] = $this->createUserWithNotifications();

        $this->client->loginUser($user);
        $this->client->request('PATCH', '/api/notifications/read-all');
        $this->assertResponseStatusCodeSame(204);

        $unread = $this->entityManager->getRepository(Notification::class)
            ->findBy(['user' => $user, 'isRead' => false]);
        $this->assertCount(0, $unread);
    }

    public function testStream(): void
    {
        [$user] = $this->createUserWithNotifications();

        $this->client->loginUser($user);
        $this->client->request('GET', '/api/notifications/stream');
        $this->assertResponseIsSuccessful();
        $type = $this->client->getResponse()->headers->get('Content-Type');
        $this->assertNotNull($type);
        $this->assertStringStartsWith('text/event-stream', $type);
        // content may be streamed; ensure response is generated
        $this->assertNotNull($this->client->getResponse());
    }
}
