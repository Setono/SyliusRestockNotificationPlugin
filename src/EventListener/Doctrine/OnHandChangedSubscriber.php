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
    public function __construct(private readonly MessageBusInterface $eventBus)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate,
        ];
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs): void
    {
        $productVariant = $eventArgs->getObject();
        if (!$productVariant instanceof ProductVariantInterface || !$productVariant instanceof StockableInterface) {
            return;
        }

        if (!$eventArgs->hasChangedField('onHand') && !$eventArgs->hasChangedField('onHold')) {
            return;
        }

        $oldOnHand = $newOnHand = (int) $productVariant->getOnHand();
        if ($eventArgs->hasChangedField('onHand')) {
            $oldOnHand = (int) $eventArgs->getOldValue('onHand');
            $newOnHand = (int) $eventArgs->getNewValue('onHand');
        }

        $oldOnHold = $newOnHold = (int) $productVariant->getOnHold();
        if ($eventArgs->hasChangedField('onHold')) {
            $oldOnHold = (int) $eventArgs->getOldValue('onHold');
            $newOnHold = (int) $eventArgs->getNewValue('onHold');
        }

        // if the old stock is greater than 0, then the product isn't restocked (it was in stock)
        $oldStock = $oldOnHand - $oldOnHold;
        if ($oldStock > 0) {
            return;
        }

        // if the new stock isn't greater than 0 then it isn't restocked (obviously)
        $newStock = $newOnHand - $newOnHold;
        if ($newStock <= 0) {
            return;
        }

        $this->eventBus->dispatch(new ProductVariantRestocked((int) $productVariant->getId()));
    }
}
