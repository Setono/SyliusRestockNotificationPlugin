<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Resolver;

use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Product\Model\ProductInterface;

final class OutOfStockResolver implements OutOfStockResolverInterface
{
    /**
     * Returns true if one or more variants are tracked AND out of stock on the given product
     */
    public function hasVariantsOutOfStock(ProductInterface $product): bool
    {
        return count($this->getOutOfStockVariants($product)) > 0;
    }

    public function getOutOfStockVariants(ProductInterface $product): array
    {
        $outOfStockVariants = [];

        foreach ($product->getVariants() as $productVariant) {
            if (!$productVariant instanceof StockableInterface) {
                continue;
            }

            if ($productVariant->isTracked() && $productVariant->getOnHand() <= 0) {
                $outOfStockVariants[] = $productVariant;
            }
        }

        return $outOfStockVariants;
    }
}
