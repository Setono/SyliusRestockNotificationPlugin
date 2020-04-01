<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Notifier;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;
use function Safe\sprintf;
use Setono\SyliusRestockNotificationPlugin\Model\NotificationInterface;
use Setono\SyliusRestockNotificationPlugin\Workflow\NotificationWorkflow;
use Sylius\Component\Inventory\Model\StockableInterface;
use Symfony\Component\Workflow\Registry;

final class Notifier implements NotifierInterface
{
    /** @var Registry */
    private $workflowRegistry;

    /** @var ManagerRegistry */
    private $managerRegistry;

    public function __construct(Registry $workflowRegistry, ManagerRegistry $managerRegistry)
    {
        $this->workflowRegistry = $workflowRegistry;
        $this->managerRegistry = $managerRegistry;
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

        $stateMachine = $this->workflowRegistry->get($notification, NotificationWorkflow::NAME); // todo get the workflow name from constant
        if (!$stateMachine->can($notification, NotificationWorkflow::TRANSITION_PROCESS)) {
            return; // todo throw exception instead?
        }

        $stateMachine->apply($notification, NotificationWorkflow::TRANSITION_PROCESS);
        $manager->flush();

        // todo send notification

        $stateMachine->apply($notification, NotificationWorkflow::TRANSITION_SEND);
        $manager->flush();
    }

    private function getManager(object $object): ObjectManager
    {
        $manager = $this->managerRegistry->getManagerForClass(get_class($object));
        if (null === $manager) {
            throw new RuntimeException(sprintf('The class %s does not have a manager associated with it', get_class($object)));
        }

        return $manager;
    }
}
