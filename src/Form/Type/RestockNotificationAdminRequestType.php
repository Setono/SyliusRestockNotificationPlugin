<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

final class RestockNotificationAdminRequestType extends RestockNotificationRequestType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('productVariant', ProductVariantAutocompleteChoiceType::class, [
                'label' => 'setono_sylius_restock_notification.form.restock_notification_request.product_variant',
            ])
        ;
    }
}
