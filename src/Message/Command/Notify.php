<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Message\Command;

final class Notify implements CommandInterface
{
    public function __construct(private readonly int $notificationId)
    {
    }

    public function getNotificationId(): int
    {
        return $this->notificationId;
    }
}
