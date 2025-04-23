<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Message\Event;

use Sylius\Component\Product\Model\ProductVariantInterface;

/**
 * Only fire this event when a product variant's on hand value went from less
 * than or equal to 0 to positive, for example, from 0 to 10 or from -2 to 5
 */
final class ProductVariantRestocked implements EventInterface
{
    public readonly int $productVariant;

    public function __construct(ProductVariantInterface|int $productVariant)
    {
        if ($productVariant instanceof ProductVariantInterface) {
            $productVariant = (int) $productVariant->getId();
        }

        $this->productVariant = $productVariant;
    }
}
