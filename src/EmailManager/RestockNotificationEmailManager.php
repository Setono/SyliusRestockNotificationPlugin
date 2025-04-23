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

    public function sendRestockNotificationEmail(RestockNotificationRequestInterface $notification): void
    {
        $channel = $notification->getChannel();
        Assert::notNull($channel);

        $locale = $notification->getLocale();
        Assert::notNull($locale);

        $localeCode = $locale->getCode();
        Assert::notNull($localeCode);

        $email = $notification->getEmail();
        Assert::notNull($email);

        /** @psalm-suppress DeprecatedMethod */
        $this->sender->send(
            Emails::RESTOCK_NOTIFICATION,
            [$email],
            [
                'notification' => $notification,
                'channel' => $channel,
                'localeCode' => $localeCode,
            ],
        )
        ;
    }
}
