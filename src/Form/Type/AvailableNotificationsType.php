<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Product\Model\ProductInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AvailableNotificationsType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productVariant', OutOfStockProductVariantChoiceType::class, [
                'label' => 'setono_sylius_restock_notification.form.available_notifications.product_variant',
                'product' => $options['product'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'setono_sylius_restock_notification.form.available_notifications.email',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['product'])
            ->setAllowedTypes('product', ProductInterface::class)
        ;
    }
}
