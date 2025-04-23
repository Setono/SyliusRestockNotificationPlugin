<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Message\Handler;

use Setono\SyliusRestockNotificationPlugin\Message\Command\Notify;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Setono\SyliusRestockNotificationPlugin\Notifier\NotifierInterface;
use Setono\SyliusRestockNotificationPlugin\Repository\RestockNotificationRequestRepositoryInterface;

final class NotifyHandler
{
    public function __construct(
        private readonly RestockNotificationRequestRepositoryInterface $restockNotificationRequestRepository,
        private readonly NotifierInterface $notifier,
    ) {
    }

    public function __invoke(Notify $message): void
    {
        $restockNotificationRequest = $this->restockNotificationRequestRepository->findOneByIdInState(
            $message->restockNotificationRequest,
            RestockNotificationRequestInterface::STATE_PENDING,
        );

        if (null === $restockNotificationRequest) {
            return;
        }

        $this->notifier->notify($restockNotificationRequest);
    }
}
