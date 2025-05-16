<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class RestockNotificationShopRequestType extends AbstractResourceType
{
    /**
     * @param class-string $dataClass
     * @param list<string> $validationGroups
     */
    public function __construct(
        private readonly ProductVariantRepositoryInterface $productVariantRepository,
        string $dataClass,
        array $validationGroups = [],
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('email', EmailType::class, [
                'label' => 'sylius.ui.email',
            ])
            ->add('productVariant', HiddenType::class, [
                'attr' => [
                    'class' => 'product-variant',
                ],
            ])
        ;

        $builder
            ->get('productVariant')
            ->addModelTransformer(new ResourceToIdentifierTransformer($this->productVariantRepository, 'code'))
        ;
    }
}
