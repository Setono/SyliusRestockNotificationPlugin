<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Repository;

use Setono\SyliusRestockNotificationPlugin\Model\NotificationInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface NotificationRepositoryInterface extends RepositoryInterface
{
    public function findOneByIdInState(int $id, string $state): ?NotificationInterface;

    /**
     * @return NotificationInterface[]
     */
    public function findByProductVariant(ProductVariantInterface $productVariant): array;

    /**
     * Will return true if a notification with the same email and product variant is present
     */
    public function hasNotification(NotificationInterface $notification): bool;
}
