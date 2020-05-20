<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\EmailManager;

use Setono\SyliusRestockNotificationPlugin\Model\NotificationInterface;

interface RestockNotificationEmailManagerInterface
{
    public function sendRestockNotificationEmail(NotificationInterface $notification): void;
}
