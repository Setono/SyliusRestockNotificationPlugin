<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class NotificationAdminType extends NotificationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            // todo add product variant autocomplete
            ->add('productVariant', TextType::class, [
                'label' => 'setono_sylius_restock_notification.form.notification.product_variant',
            ])
        ;
    }
}
