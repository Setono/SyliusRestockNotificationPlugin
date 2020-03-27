<?php

declare(strict_types=1);

namespace spec\Setono\SyliusRestockNotificationPlugin\UrlGenerator;

use PhpSpec\ObjectBehavior;
use Setono\SyliusRestockNotificationPlugin\UrlGenerator\ProductUrlGenerator;
use Setono\SyliusRestockNotificationPlugin\UrlGenerator\ProductVariantUrlGeneratorInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductUrlGeneratorSpec extends ObjectBehavior
{
    public function let(UrlGeneratorInterface $urlGenerator): void
    {
        $this->beConstructedWith($urlGenerator);
    }

    public function it_implements_product_variant_url_generator_interface(): void
    {
        $this->shouldImplement(ProductVariantUrlGeneratorInterface::class);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductUrlGenerator::class);
    }

    public function it_generates_correct_url(
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        UrlGeneratorInterface $urlGenerator
    ): void {
        $productVariant->getProduct()->willReturn($product);
        $product->getSlug()->willReturn('slug');

        $urlGenerator->generate('sylius_shop_product_show', [
            'utm_source' => 'test',
            'slug' => 'slug',
        ], UrlGeneratorInterface::ABSOLUTE_PATH)->willReturn('/product/slug?utm_source=test');

        $this->generate($productVariant, [
            'utm_source' => 'test',
        ])->shouldReturn('/product/slug?utm_source=test');
    }
}
