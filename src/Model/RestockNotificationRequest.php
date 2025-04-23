<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Model;

use DateTimeInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Webmozart\Assert\Assert;

class RestockNotificationRequest implements RestockNotificationRequestInterface
{
    use TimestampableTrait;

    protected int $version = 1;

    protected ?int $id = null;

    protected ?ChannelInterface $channel = null;

    protected ?LocaleInterface $locale = null;

    protected string $state = self::STATE_PENDING;

    protected ?ProductVariantInterface $productVariant = null;

    protected ?string $email = null;

    protected ?DateTimeInterface $sentAt = null;

    public static function getStates(): array
    {
        return [
            self::STATE_PENDING => self::STATE_PENDING,
            self::STATE_PROCESSING => self::STATE_PROCESSING,
            self::STATE_SENT => self::STATE_SENT,
            self::STATE_FAILED => self::STATE_FAILED,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setLocale(LocaleInterface $locale): void
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

    public function sentAt(): ?DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(DateTimeInterface $sentAt): void
    {
        $this->sentAt = $sentAt;
    }
}
