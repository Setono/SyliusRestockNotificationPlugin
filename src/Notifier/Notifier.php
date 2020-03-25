<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Notifier;

use Setono\SyliusRestockNotificationPlugin\Model\NotificationInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Symfony\Component\Workflow\Registry;

final class Notifier implements NotifierInterface
{
    /** @var Registry */
    private $workflowRegistry;

    public function __construct(Registry $workflowRegistry)
    {
        $this->workflowRegistry = $workflowRegistry;
    }

    public function notify(NotificationInterface $notification): void
    {
        $stateMachine = $this->workflowRegistry->get($notification, 'notification'); // todo get the workflow name from constant
        if (!$stateMachine->can($notification, 'send')) {
            return; // todo throw exception instead?
        }

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

        // the locale is null, so we get the default locale from the channel
        if (null === $locale) {
            if (!$channel instanceof ChannelInterface) {
                return;
            }

            $locale = $channel->getDefaultLocale();
            if (null === $locale) {
                return;
            }
        }

        $email = $notification->getEmail();
        if (null === $email) {
            return;
        }

        // todo send notification
    }
}
