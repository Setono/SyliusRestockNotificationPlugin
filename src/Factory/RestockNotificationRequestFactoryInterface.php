<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Factory;

use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface RestockNotificationRequestFactoryInterface extends FactoryInterface
{
    public function createNew(): RestockNotificationRequestInterface;

    /**
     * Will create a new restock notification request where the channel and locale code
     * is already set with the values given by the respective contexts
     */
    public function createWithChannelAndLocaleContext(): RestockNotificationRequestInterface;
}
