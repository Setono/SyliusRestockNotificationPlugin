<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Resolver;

use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class OutOfStockResolver implements OutOfStockResolverInterface
{
    /** @var ProductVariantInterface[][] */
    private $cache = [];

    /** @var AvailabilityCheckerInterface */
    private $availabilityChecker;

    public function __construct(AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->availabilityChecker = $availabilityChecker;
    }

    public function hasVariantsOutOfStock(ProductInterface $product): bool
    {
        return count($this->getOutOfStockVariants($product)) > 0;
    }

    public function getOutOfStockVariants(ProductInterface $product): array
    {
        $code = $product->getCode();
        Assert::notNull($code);

        if (!isset($this->cache[$code])) {
            $outOfStockVariants = [];

            foreach ($product->getVariants() as $productVariant) {
                if (!$productVariant instanceof StockableInterface) {
                    continue;
                }

                if (!$this->availabilityChecker->isStockAvailable($productVariant)) {
                    $outOfStockVariants[] = $productVariant;
                }
            }

            $this->cache[$code] = $outOfStockVariants;
        }

        return $this->cache[$code];
    }
}
