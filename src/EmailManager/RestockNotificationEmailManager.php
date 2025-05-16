<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\EmailManager;

use Setono\SyliusRestockNotificationPlugin\Mailer\Emails;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Webmozart\Assert\Assert;

final class RestockNotificationEmailManager implements RestockNotificationEmailManagerInterface
{
    public function __construct(private readonly SenderInterface $sender)
    {
    }

    public function sendRestockNotificationEmail(RestockNotificationRequestInterface $restockNotificationRequest): void
    {
        $channel = $restockNotificationRequest->getChannel();
        Assert::notNull($channel);

        $localeCode = $restockNotificationRequest->getLocaleCode();
        Assert::notNull($localeCode);

        $email = $restockNotificationRequest->getEmail();
        Assert::notNull($email);

        /** @psalm-suppress DeprecatedMethod */
        $this->sender->send(
            Emails::RESTOCK_NOTIFICATION_REQUEST,
            [$email],
            [
                'restockNotificationRequest' => $restockNotificationRequest,
                'channel' => $channel,
                'localeCode' => $localeCode,
            ],
        )
        ;
    }
}
