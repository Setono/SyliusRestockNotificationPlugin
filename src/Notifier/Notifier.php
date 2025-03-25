<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Notifier;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;
use Setono\SyliusRestockNotificationPlugin\EmailManager\RestockNotificationEmailManagerInterface;
use Setono\SyliusRestockNotificationPlugin\Model\NotificationInterface;
use Setono\SyliusRestockNotificationPlugin\Workflow\NotificationWorkflow;
use Sylius\Component\Inventory\Model\StockableInterface;
use Symfony\Component\Workflow\Registry;

final class Notifier implements NotifierInterface
{
    public function __construct(private readonly Registry $workflowRegistry, private readonly ManagerRegistry $managerRegistry, private readonly RestockNotificationEmailManagerInterface $restockNotificationEmailManager)
    {
    }

    public function notify(NotificationInterface $notification): void
    {
        $productVariant = $notification->getProductVariant();
        if (!$productVariant instanceof StockableInterface) {
            return;
        }

        $onHand = $productVariant->getOnHand();
        if ($onHand === null || $onHand <= 0) {
            return;
        }

        $channel = $notification->getChannel();
        if (null === $channel) {
            return;
        }

        $locale = $notification->getLocale();

        if (null === $locale) {
            return;
        }

        $email = $notification->getEmail();
        if (null === $email) {
            return;
        }

        $manager = $this->getManager($notification);

        $stateMachine = $this->workflowRegistry->get($notification, NotificationWorkflow::NAME);
        if (!$stateMachine->can($notification, NotificationWorkflow::TRANSITION_PROCESS)) {
            return; // todo throw exception instead?
        }

        $stateMachine->apply($notification, NotificationWorkflow::TRANSITION_PROCESS);
        $manager->flush();

        $this->restockNotificationEmailManager->sendRestockNotificationEmail($notification);

        $stateMachine->apply($notification, NotificationWorkflow::TRANSITION_SEND);
        $manager->flush();
    }

    private function getManager(object $object): ObjectManager
    {
        $manager = $this->managerRegistry->getManagerForClass($object::class);
        if (null === $manager) {
            throw new RuntimeException(sprintf(
                'The class %s does not have a manager associated with it',
                $object::class,
            ));
        }

        return $manager;
    }
}
