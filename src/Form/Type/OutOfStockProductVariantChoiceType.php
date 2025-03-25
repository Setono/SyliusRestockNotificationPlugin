<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Form\Type;

use Setono\SyliusRestockNotificationPlugin\Resolver\OutOfStockResolverInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OutOfStockProductVariantChoiceType extends AbstractType
{
    public function __construct(private readonly OutOfStockResolverInterface $outOfStockResolver)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('choices', fn (Options $options): array => $this->outOfStockResolver->getOutOfStockVariants($options['product']))
            ->setDefault('choice_label', static function (ProductVariantInterface $productVariant): string {
                $str = '';

                if (count($productVariant->getOptionValues()) > 0) {
                    $optionValues = [];

                    foreach ($productVariant->getOptionValues() as $optionValue) {
                        $optionValues[] = $optionValue->getValue();
                    }

                    $str = implode(' | ', $optionValues);
                }

                if ('' === $str) {
                    $str = (string) $productVariant->getName();

                    $product = $productVariant->getProduct();
                    if (null !== $product) {
                        $str = $product->getName() . '(' . $str . ')';
                    }
                }

                return $str;
            })
            ->setDefault('choice_value', 'code')
            ->setRequired(['product'])
            ->setAllowedTypes('product', ProductInterface::class)
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
