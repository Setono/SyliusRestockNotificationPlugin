<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Repository;

use DateTimeInterface;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface RestockNotificationRequestRepositoryInterface extends RepositoryInterface
{
    public function findOneByIdInState(int $id, string $state): ?RestockNotificationRequestInterface;

    /**
     * @return RestockNotificationRequestInterface[]
     */
    public function findByProductVariant(ProductVariantInterface $productVariant): array;

    /**
     * Will return true if a restock notification request with the same email and product variant is present
     */
    public function hasRestockNotificationRequest(RestockNotificationRequestInterface $notification): bool;

    /**
     * Remove restock notification requests created before the given date
     *
     * @return int The number of deleted requests
     */
    public function removeOlderThan(DateTimeInterface $date): int;
}
