<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Notifier;

use Setono\SyliusRestockNotificationPlugin\Model\NotificationInterface;

interface NotifierInterface
{
    public function notify(NotificationInterface $notification): void;
}
