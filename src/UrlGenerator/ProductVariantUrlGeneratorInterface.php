<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\UrlGenerator;

use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface ProductVariantUrlGeneratorInterface
{
    /**
     * The parameter $parameters will be forwarded to the underlying url generator. This could be used
     * to add tracking parameters like utm_source, utm_medium, and utm_campaign
     */
    public function generate(
        ProductVariantInterface $productVariant,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string;
}
