<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\EmailManager;

use Setono\SyliusRestockNotificationPlugin\Mailer\Emails;
use Setono\SyliusRestockNotificationPlugin\Model\NotificationInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Webmozart\Assert\Assert;

final class RestockNotificationEmailManager implements RestockNotificationEmailManagerInterface
{
    /** @var SenderInterface */
    private $sender;

    public function __construct(SenderInterface $sender)
    {
        $this->sender = $sender;
    }

    public function sendRestockNotificationEmail(NotificationInterface $notification): void
    {
        $channel = $notification->getChannel();
        Assert::notNull($channel);

        $locale = $notification->getLocale();
        Assert::notNull($locale);

        $localeCode = $locale->getCode();
        Assert::notNull($localeCode);

        $email = $notification->getEmail();
        Assert::notNull($email);

        $this->sender->send(
            Emails::RESTOCK_NOTIFICATION,
            [$email],
            [
                'notification' => $notification,
                'channel' => $channel,
                'localeCode' => $localeCode,
            ])
        ;
    }
}
