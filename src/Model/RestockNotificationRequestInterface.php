<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Model;

use DateTimeInterface;
use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface RestockNotificationRequestInterface extends ResourceInterface, TimestampableInterface, ChannelAwareInterface
{
    public const STATE_PENDING = 'pending';

    public const STATE_PROCESSING = 'processing';

    public const STATE_SENT = 'sent';

    public const STATE_ERROR = 'error';

    public function getId(): ?int;

    public function getLocale(): ?LocaleInterface;

    public function setLocale(LocaleInterface $locale): void;

    public function getState(): string;

    public function setState(string $state): void;

    public function getProductVariant(): ?ProductVariantInterface;

    public function setProductVariant(ProductVariantInterface $productVariant): void;

    public function getEmail(): ?string;

    public function setEmail(string $email): void;

    /**
     * Returns the date and time the notification was sent or null if it hasn't been sent yet
     */
    public function sentAt(): ?DateTimeInterface;

    public function setSentAt(DateTimeInterface $sentAt): void;
}
