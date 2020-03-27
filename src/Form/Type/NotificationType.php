<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class NotificationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channel', ChannelChoiceType::class, [
                'label' => 'setono_sylius_restock_notification.form.notification.channel',
            ])
            ->add('locale', LocaleChoiceType::class, [
                'label' => 'setono_sylius_restock_notification.form.notification.locale',
                'required' => false,
            ])
            // todo add product variant autocomplete
            ->add('productVariant', TextType::class, [
                'label' => 'setono_sylius_restock_notification.form.notification.product_variant',
            ])
            ->add('email', EmailType::class, [
                'label' => 'setono_sylius_restock_notification.form.notification.email',
            ])
        ;
    }
}
