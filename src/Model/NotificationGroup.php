<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Model;

final class NotificationGroup
{
    /** @var string */
    private $productVariant;

    /** @var int */
    private $count;

    public function __construct(string $productVariant, int $count)
    {
        $this->productVariant = $productVariant;
        $this->count = $count;
    }

    public function getProductVariant(): string
    {
        return $this->productVariant;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
