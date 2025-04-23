<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Message\Command;

use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;

final class Notify implements CommandInterface
{
    public readonly int $restockNotificationRequest;

    public function __construct(RestockNotificationRequestInterface|int $restockNotificationRequest)
    {
        if ($restockNotificationRequest instanceof RestockNotificationRequestInterface) {
            $restockNotificationRequest = (int) $restockNotificationRequest->getId();
        }

        $this->restockNotificationRequest = $restockNotificationRequest;
    }
}
