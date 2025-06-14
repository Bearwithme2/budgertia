<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Service\JsonApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/notifications')]
class NotificationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JsonApi $jsonApi
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $page = max(1, (int) $request->query->get('page', '1'));
        $limit = max(1, (int) $request->query->get('limit', '10'));
        $isRead = $request->query->get('isRead', 'false') === 'true';

        $qb = $this->entityManager->getRepository(Notification::class)
            ->createQueryBuilder('n')
            ->where('n.user = :user')
            ->andWhere('n.isRead = :isRead')
            ->setParameter('user', $user)
            ->setParameter('isRead', $isRead)
            ->orderBy('n.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        /** @var array<int, Notification> $notifications */
        $notifications = $qb->getQuery()->getResult();

        $items = [];
        foreach ($notifications as $notification) {
            if (!$notification instanceof Notification) {
                continue;
            }
            $items[] = [
                'type' => 'notification',
                'id' => (string) $notification->getId(),
                'attributes' => [
                    'message' => $notification->getMessage(),
                    'level' => $notification->getLevel(),
                    'createdAt' => $notification->getCreatedAt()->format(DATE_ATOM),
                    'isRead' => $notification->isRead(),
                ],
            ];
        }

        return new JsonResponse($this->jsonApi->collection($items));
    }

    #[Route('/{id}/read', methods: ['PATCH'])]
    public function markRead(Notification $notification): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($notification->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['errors' => [['detail' => 'Not found']]], 404);
        }

        $notification->setIsRead(true);
        $this->entityManager->flush();

        $id = $notification->getId();
        \assert(is_int($id));

        return new JsonResponse(
            $this->jsonApi->item('notification', $id, [
                'message' => $notification->getMessage(),
                'level' => $notification->getLevel(),
                'createdAt' => $notification->getCreatedAt()->format(DATE_ATOM),
                'isRead' => $notification->isRead(),
            ])
        );
    }

    #[Route('/read-all', methods: ['PATCH'])]
    public function markAllRead(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $this->entityManager->createQuery(
            'UPDATE App\\Entity\\Notification n SET n.isRead = true WHERE n.user = :user AND n.isRead = false'
        )->setParameter('user', $user)->execute();

        return new JsonResponse(null, 204);
    }

    #[Route('/stream', methods: ['GET'])]
    public function streamEvents(): StreamedResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $notifications = $this->entityManager->getRepository(Notification::class)
            ->findBy(['user' => $user, 'isRead' => false], ['id' => 'ASC']);

        $response = new StreamedResponse(function () use ($notifications): void {
            foreach ($notifications as $notification) {
                if (!$notification instanceof Notification) {
                    continue;
                }
                $id = $notification->getId();
                \assert(is_int($id));
                $data = json_encode([
                    'message' => $notification->getMessage(),
                    'level' => $notification->getLevel(),
                    'createdAt' => $notification->getCreatedAt()->format(DATE_ATOM),
                    'isRead' => $notification->isRead(),
                ]);
                echo "id: {$id}\n";
                echo "data: {$data}\n\n";
                ob_flush();
                flush();
            }
        });
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }
}
