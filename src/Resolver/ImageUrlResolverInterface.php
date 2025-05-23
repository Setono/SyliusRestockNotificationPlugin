<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Resolver;

use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;

interface ImageUrlResolverInterface
{
    /**
     * Will resolve an image URL based on a restock notification request
     */
    public function resolve(RestockNotificationRequestInterface $restockNotificationRequest): ?string;
}
