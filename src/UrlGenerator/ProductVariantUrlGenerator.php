<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\UrlGenerator;

use Setono\SyliusVariantLinkPlugin\UrlGenerator\ProductVariantUrlGeneratorInterface as VariantLinkPluginProductVariantUrlGeneratorInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductVariantUrlGenerator implements ProductVariantUrlGeneratorInterface
{
    /** @var VariantLinkPluginProductVariantUrlGeneratorInterface */
    private $productVariantUrlGenerator;

    public function __construct(VariantLinkPluginProductVariantUrlGeneratorInterface $productVariantUrlGenerator)
    {
        $this->productVariantUrlGenerator = $productVariantUrlGenerator;
    }

    public function generate(
        ProductVariantInterface $productVariant,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        return $this->productVariantUrlGenerator->generate($productVariant, $parameters, $referenceType);
    }
}
