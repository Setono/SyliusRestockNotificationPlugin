<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Webmozart\Assert\Assert;

class Notification implements NotificationInterface
{
    use TimestampableTrait;

    /** @var int */
    protected $id;

    /** @var ChannelInterface */
    protected $channel;

    /** @var LocaleInterface|null */
    protected $locale;

    /** @var string */
    protected $state = self::STATE_PENDING;

    /** @var ProductVariantInterface */
    protected $productVariant;

    /** @var string */
    protected $email;

    public static function getStates(): array
    {
        return [
            self::STATE_PENDING => self::STATE_PENDING,
            self::STATE_SENT => self::STATE_SENT,
            self::STATE_ERROR => self::STATE_ERROR,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        Assert::notNull($channel);

        $this->channel = $channel;
    }

    public function getLocale(): ?LocaleInterface
    {
        return $this->locale;
    }

    public function setLocale(?LocaleInterface $locale): void
    {
        $this->locale = $locale;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getProductVariant(): ?ProductVariantInterface
    {
        return $this->productVariant;
    }

    public function setProductVariant(ProductVariantInterface $productVariant): void
    {
        $this->productVariant = $productVariant;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
