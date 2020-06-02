<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Workflow;

use Setono\SyliusRestockNotificationPlugin\Model\Notification;

final class NotificationWorkflow
{
    public const NAME = 'restock_notification';

    public const TRANSITION_PROCESS = 'process';

    public const TRANSITION_SEND = 'send';

    public const TRANSITION_ERROR = 'error';

    public const TRANSITION_RESEND = 'resend';

    public const TRANSITION_RETRY = 'retry';

    public static function getStates(): array
    {
        return array_values(Notification::getStates());
    }
}
