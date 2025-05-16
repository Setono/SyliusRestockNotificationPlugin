<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Factory;

use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class RestockNotificationRequestFactory implements RestockNotificationRequestFactoryInterface
{
    public function __construct(
        private readonly FactoryInterface $decorated,
        private readonly ChannelContextInterface $channelContext,
        private readonly LocaleContextInterface $localeContext,
    ) {
    }

    public function createNew(): RestockNotificationRequestInterface
    {
        $obj = $this->decorated->createNew();
        Assert::isInstanceOf($obj, RestockNotificationRequestInterface::class);

        return $obj;
    }

    public function createWithChannelAndLocaleContext(): RestockNotificationRequestInterface
    {
        $obj = $this->createNew();
        $obj->setChannel($this->channelContext->getChannel());
        $obj->setLocaleCode($this->localeContext->getLocaleCode());

        return $obj;
    }
}
