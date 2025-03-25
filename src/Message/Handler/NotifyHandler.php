<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Message\Handler;

use Setono\SyliusRestockNotificationPlugin\Message\Command\Notify;
use Setono\SyliusRestockNotificationPlugin\Model\NotificationInterface;
use Setono\SyliusRestockNotificationPlugin\Notifier\NotifierInterface;
use Setono\SyliusRestockNotificationPlugin\Repository\NotificationRepositoryInterface;

final class NotifyHandler
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notificationRepository,
        private readonly NotifierInterface $notifier,
    ) {
    }

    public function __invoke(Notify $message): void
    {
        $notification = $this->notificationRepository->findOneByIdInState(
            $message->getNotificationId(),
            NotificationInterface::STATE_PENDING,
        );

        if (null === $notification) {
            return;
        }

        $this->notifier->notify($notification);
    }
}
