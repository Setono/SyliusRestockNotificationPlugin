<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Message\Handler;

use Setono\SyliusRestockNotificationPlugin\Message\Command\Notify;
use Setono\SyliusRestockNotificationPlugin\Notifier\NotifierInterface;
use Setono\SyliusRestockNotificationPlugin\Repository\NotificationRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class NotifyHandler implements MessageHandlerInterface
{
    /** @var NotificationRepositoryInterface */
    private $notificationRepository;

    /** @var NotifierInterface */
    private $notifier;

    public function __construct(NotificationRepositoryInterface $notificationRepository, NotifierInterface $notifier)
    {
        $this->notificationRepository = $notificationRepository;
        $this->notifier = $notifier;
    }

    public function __invoke(Notify $message): void
    {
        $notification = $this->notificationRepository->findOneByIdInState($message->getNotificationId());

        if (null === $notification) {
            return;
        }

        $this->notifier->notify($notification);
    }
}
