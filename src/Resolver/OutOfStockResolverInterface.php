<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Resolver;

use Sylius\Component\Product\Model\ProductInterface;

interface OutOfStockResolverInterface
{
    /**
     * Returns true if one or more variants are tracked AND out of stock on the given product
     */
    public function hasVariantsOutOfStock(ProductInterface $product): bool;

    public function getOutOfStockVariants(ProductInterface $product): array;
}
