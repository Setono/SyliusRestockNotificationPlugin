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
    /** @var OutOfStockResolverInterface */
    private $outOfStockResolver;

    public function __construct(OutOfStockResolverInterface $outOfStockResolver)
    {
        $this->outOfStockResolver = $outOfStockResolver;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('choices', function (Options $options): array {
                return $this->outOfStockResolver->getOutOfStockVariants($options['product']);
            })
            ->setDefault('choice_label', static function (ProductVariantInterface $productVariant): string {
                $label = (string) $productVariant->getName();

                $product = $productVariant->getProduct();
                if (null !== $product) {
                    $label = $product->getName() . ' - ' . $label;
                }

                return $label; // todo add this to a service
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
