<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\UrlGenerator;

use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

/**
 * This is the default implementation for Sylius which will link to a Sylius product
 * because standard Sylius doesn't provide direct links to variants.
 *
 * If you want to link directly to variants, you can install the variant link plugin (https://github.com/Setono/SyliusVariantLinkPlugin)
 * and instead use the Setono\SyliusRestockNotificationPlugin\UrlGenerator\ProductVariantUrlGenerator
 */
final class ProductUrlGenerator implements ProductVariantUrlGeneratorInterface
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function generate(
        ProductVariantInterface $productVariant,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        $product = $productVariant->getProduct();
        Assert::notNull($product);

        $slug = $product->getSlug();
        Assert::notNull($slug);

        $parameters = array_merge($parameters, [
            'slug' => $slug,
        ]);

        return $this->urlGenerator->generate('sylius_shop_product_show', $parameters, $referenceType);
    }
}
