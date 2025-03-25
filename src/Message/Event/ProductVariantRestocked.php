<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Message\Event;

/**
 * Only fire this event when a product variant's on hand value went from less than 0 to positive,
 * for example from 0 to 10 or from -2 to 5
 */
final class ProductVariantRestocked implements EventInterface
{
    public function __construct(private readonly int $productVariantId)
    {
    }

    public function getProductVariantId(): int
    {
        return $this->productVariantId;
    }
}
