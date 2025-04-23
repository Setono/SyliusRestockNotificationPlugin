<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\EmailManager;

use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;

interface RestockNotificationEmailManagerInterface
{
    public function sendRestockNotificationEmail(RestockNotificationRequestInterface $notification): void;
}
