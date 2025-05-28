<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Tests\Resolver;

use Doctrine\Common\Collections\Collection;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Setono\SyliusRestockNotificationPlugin\Resolver\ImageUrlResolver;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ImageUrlResolverTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<CacheManager> */
    private ObjectProphecy $cacheManager;

    private string $filter = 'sylius_shop_product_large_thumbnail';

    protected function setUp(): void
    {
        $this->cacheManager = $this->prophesize(CacheManager::class);
    }

    /**
     * @test
     */
    public function it_returns_null_when_product_variant_is_null(): void
    {
        $resolver = new ImageUrlResolver($this->cacheManager->reveal(), $this->filter);

        $request = $this->prophesize(RestockNotificationRequestInterface::class);
        $request->getProductVariant()->willReturn(null);

        $result = $resolver->resolve($request->reveal());

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function it_returns_null_when_product_is_null(): void
    {
        $resolver = new ImageUrlResolver($this->cacheManager->reveal(), $this->filter);

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getProduct()->willReturn(null);

        $request = $this->prophesize(RestockNotificationRequestInterface::class);
        $request->getProductVariant()->willReturn($productVariant->reveal());

        $result = $resolver->resolve($request->reveal());

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function it_returns_null_when_image_is_not_found(): void
    {
        $resolver = new ImageUrlResolver($this->cacheManager->reveal(), $this->filter);

        $images = $this->prophesize(Collection::class);
        $images->first()->willReturn(false);

        $product = $this->prophesize(ProductInterface::class);
        $product->getImages()->willReturn($images->reveal());

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getProduct()->willReturn($product->reveal());

        $request = $this->prophesize(RestockNotificationRequestInterface::class);
        $request->getProductVariant()->willReturn($productVariant->reveal());

        $result = $resolver->resolve($request->reveal());

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function it_returns_null_when_path_is_null(): void
    {
        $resolver = new ImageUrlResolver($this->cacheManager->reveal(), $this->filter);

        $image = $this->prophesize(ImageInterface::class);
        $image->getPath()->willReturn(null);

        $images = $this->prophesize(Collection::class);
        $images->first()->willReturn($image->reveal());

        $product = $this->prophesize(ProductInterface::class);
        $product->getImages()->willReturn($images->reveal());

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getProduct()->willReturn($product->reveal());

        $request = $this->prophesize(RestockNotificationRequestInterface::class);
        $request->getProductVariant()->willReturn($productVariant->reveal());

        $result = $resolver->resolve($request->reveal());

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function it_returns_browser_path_when_hostname_is_null(): void
    {
        $path = 'path/to/image.jpg';
        $browserPath = '/media/cache/resolve/sylius_shop_product_large_thumbnail/path/to/image.jpg';

        $this->cacheManager->getBrowserPath($path, $this->filter)
            ->willReturn($browserPath);

        $resolver = new ImageUrlResolver($this->cacheManager->reveal(), $this->filter);

        $image = $this->prophesize(ImageInterface::class);
        $image->getPath()->willReturn($path);

        $images = $this->prophesize(Collection::class);
        $images->first()->willReturn($image->reveal());

        $product = $this->prophesize(ProductInterface::class);
        $product->getImages()->willReturn($images->reveal());

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getProduct()->willReturn($product->reveal());

        $request = $this->prophesize(RestockNotificationRequestInterface::class);
        $request->getProductVariant()->willReturn($productVariant->reveal());
        $request->getChannel()->willReturn(null);

        $result = $resolver->resolve($request->reveal());

        $this->assertEquals($browserPath, $result);
    }

    /**
     * @test
     */
    public function it_returns_browser_path_when_hostname_is_empty(): void
    {
        $path = 'path/to/image.jpg';
        $browserPath = '/media/cache/resolve/sylius_shop_product_large_thumbnail/path/to/image.jpg';

        $this->cacheManager->getBrowserPath($path, $this->filter)
            ->willReturn($browserPath);

        $resolver = new ImageUrlResolver($this->cacheManager->reveal(), $this->filter);

        $image = $this->prophesize(ImageInterface::class);
        $image->getPath()->willReturn($path);

        $images = $this->prophesize(Collection::class);
        $images->first()->willReturn($image->reveal());

        $product = $this->prophesize(ProductInterface::class);
        $product->getImages()->willReturn($images->reveal());

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getProduct()->willReturn($product->reveal());

        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getHostname()->willReturn('');

        $request = $this->prophesize(RestockNotificationRequestInterface::class);
        $request->getProductVariant()->willReturn($productVariant->reveal());
        $request->getChannel()->willReturn($channel->reveal());

        $result = $resolver->resolve($request->reveal());

        $this->assertEquals($browserPath, $result);
    }
}
