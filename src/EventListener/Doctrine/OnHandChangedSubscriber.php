<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\EventListener\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Setono\SyliusRestockNotificationPlugin\Message\Event\ProductVariantRestocked;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class OnHandChangedSubscriber implements EventSubscriber
{
    /** @var MessageBusInterface */
    private $eventBus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate => 'handleUpdate',
        ];
    }

    public function handleUpdate(PreUpdateEventArgs $eventArgs): void
    {
        $productVariant = $eventArgs->getObject();
        if (!$productVariant instanceof ProductVariantInterface || !$productVariant instanceof StockableInterface) {
            return;
        }

        if (!$eventArgs->hasChangedField('onHand')) {
            return;
        }

        // if the old value isn't 0 or less, we don't want to send a notification,
        // because that should have been done when the old value was 0
        if ($eventArgs->getOldValue('onHand') > 0) {
            return;
        }

        // if the new value isn't greater than 0 then there is no point in sending a restock notification obviously
        if ($eventArgs->getNewValue('onHand') <= 0) {
            return;
        }

        $this->eventBus->dispatch(new ProductVariantRestocked((int) $productVariant->getId()));
    }
}
