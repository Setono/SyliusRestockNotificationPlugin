<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Notifier;

use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;

interface NotifierInterface
{
    public function notify(RestockNotificationRequestInterface $notification): void;
}
