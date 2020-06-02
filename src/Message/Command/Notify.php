<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Message\Command;

final class Notify implements CommandInterface
{
    /** @var int */
    private $notificationId;

    public function __construct(int $notificationId)
    {
        $this->notificationId = $notificationId;
    }

    public function getNotificationId(): int
    {
        return $this->notificationId;
    }
}
