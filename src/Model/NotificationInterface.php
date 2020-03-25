<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Model;

use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface NotificationInterface extends ResourceInterface, TimestampableInterface, ChannelAwareInterface
{
    public const STATE_PENDING = 'pending';

    public const STATE_PROCESSING = 'processing';

    public const STATE_SENT = 'sent';

    public const STATE_ERROR = 'error';

    public function getId(): ?int;

    /**
     * If the locale is null, use the default locale on the channel
     */
    public function getLocale(): ?LocaleInterface;

    public function setLocale(?LocaleInterface $locale): void;

    public function getState(): string;

    public function setState(string $state): void;

    public function getProductVariant(): ?ProductVariantInterface;

    public function setProductVariant(ProductVariantInterface $productVariant): void;

    public function getEmail(): ?string;

    public function setEmail(string $email): void;
}
