<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Twig;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\SyliusRestockNotificationPlugin\Form\Type\RestockNotificationShopRequestType;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

final class Runtime implements RuntimeExtensionInterface, LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly AvailabilityCheckerInterface $availabilityChecker,
        private readonly FormFactoryInterface $formFactory,
        private readonly bool $debug,
    ) {
        $this->logger = new NullLogger();
    }

    public function resources(Environment $twig, array $context, ProductInterface $product = null): string
    {
        /** @var ProductInterface|mixed|null $product */
        $product = $product ?? $context['product'] ?? null;

        if (!$product instanceof ProductInterface) {
            $this->logger->error(sprintf('Either the product is not set or it is not an instance of %s', ProductInterface::class));

            return '';
        }

        $variants = ['variants' => []];

        $optionReferences = [];

        /** @var ProductVariantInterface $variant */
        foreach ($product->getEnabledVariants() as $variant) {
            $variantCode = (string) $variant->getCode();

            $variants['variants'][$variantCode] = [
                'code' => $variantCode,
                'inStock' => $this->availabilityChecker->isStockAvailable($variant),
            ];

            $optionValues = $variant->getOptionValues()->map(static fn (ProductOptionValueInterface $optionValue) => (string) $optionValue->getCode())->toArray();
            if ([] !== $optionValues) {
                $optionReferences[] = self::permutate($optionValues, $variantCode);
            }
        }

        $variants['optionReferences'] = array_merge_recursive(...$optionReferences);

        $flags = \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT;
        if ($this->debug) {
            $flags |= \JSON_PRETTY_PRINT;
        }

        $ret = sprintf('<script type="application/json" id="ssrn-variants">%s</script>', json_encode($variants, $flags));
        $ret .= $twig->render('@SetonoSyliusRestockNotificationPlugin/shop/styles.html.twig');
        $ret .= $twig->render('@SetonoSyliusRestockNotificationPlugin/shop/scripts.html.twig', [
            'form' => $this->formFactory->create(RestockNotificationShopRequestType::class)->createView(),
        ]);

        return $ret;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param array<array-key, string> $keys
     */
    private static function permutate(array $keys, string $finalValue): array
    {
        $keys = array_values($keys);
        $c = count($keys);

        if (0 === $c) {
            return [];
        }

        if (1 === $c) {
            return [$keys[0] => $finalValue];
        }

        $result = [];
        foreach ($keys as $index => $key) {
            $result[$key] = self::permutate(array_slice($keys, 0, $index) + array_slice($keys, $index + 1), $finalValue);
        }

        return $result;
    }
}
