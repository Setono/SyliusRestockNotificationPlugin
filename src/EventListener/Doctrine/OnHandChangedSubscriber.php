<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\EventListener\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Setono\SyliusRestockNotificationPlugin\Message\Event\ProductVariantRestocked;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Messenger\MessageBusInterface;

// todo change to listener
final class OnHandChangedSubscriber implements EventSubscriber
{
    /**
     * The keys are product variant ids, and the values are just true to indicate that the product variant is set
     *
     * @var array<int, true>
     */
    private array $candidates = [];

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
        if (!$productVariant instanceof ProductVariantInterface) {
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

        // if the new stock isn't greater than 0, then it isn't restocked (obviously)
        $newStock = $newOnHand - $newOnHold;
        if ($newStock <= 0) {
            return;
        }

        $this->candidates[(int) $productVariant->getId()] = true;
    }

    public function postUpdate(PostUpdateEventArgs $eventArgs): void
    {
        $productVariant = $eventArgs->getObject();
        if (!$productVariant instanceof ProductVariantInterface) {
            return;
        }

        $id = (int) $productVariant->getId();

        if (!isset($this->candidates[$id])) {
            return;
        }

        unset($this->candidates[$id]);

        $this->eventBus->dispatch(new ProductVariantRestocked($id));
    }
}
