<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\EventListener;

use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Setono\SyliusRestockNotificationPlugin\Workflow\NotificationWorkflow;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Webmozart\Assert\Assert;

final class UpdateSentAtSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            sprintf('workflow.%s.completed.%s', NotificationWorkflow::NAME, NotificationWorkflow::TRANSITION_SEND) => 'update',
        ];
    }

    public function update(CompletedEvent $event): void
    {
        /** @var RestockNotificationRequestInterface|object $notification */
        $notification = $event->getSubject();
        Assert::isInstanceOf($notification, RestockNotificationRequestInterface::class);

        $notification->setSentAt(new \DateTimeImmutable());
    }
}
