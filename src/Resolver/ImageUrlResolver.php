<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Resolver;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class ImageUrlResolver implements ImageUrlResolverInterface
{
    public function __construct(
        private readonly CacheManager $cacheManager,
        private readonly string $filter = 'sylius_shop_product_large_thumbnail',
    ) {
    }

    public function resolve(RestockNotificationRequestInterface $restockNotificationRequest): ?string
    {
        $product = $restockNotificationRequest->getProductVariant()?->getProduct();
        if (!$product instanceof ProductInterface) {
            return null;
        }

        $image = $product->getImages()->first();
        if (false === $image) {
            return null;
        }

        $path = $image->getPath();
        if (null === $path) {
            return null;
        }

        return $this->cacheManager->getBrowserPath($path, $this->filter);
    }

    /**
     * @return non-empty-string|null
     */
    private static function getChannelHostname(RestockNotificationRequestInterface $restockNotificationRequest): ?string
    {
        $channel = $restockNotificationRequest->getChannel();
        if (null === $channel) {
            return null;
        }

        $hostname = $channel->getHostname();
        if (null === $hostname || '' === $hostname) {
            return null;
        }

        return sprintf('https://%s', $hostname);
    }
}
