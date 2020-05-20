<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Repository;

use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface as BaseProductVariantRepositoryInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

interface ProductVariantRepositoryInterface extends BaseProductVariantRepositoryInterface
{
    /**
     * This method is used in the auto completion of product variants when adding a restock notification manually
     *
     * @return ProductVariantInterface[]
     */
    public function findByPhrase(string $phrase, string $locale): array;
}
