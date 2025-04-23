<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class RestockNotificationRequestType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channel', ChannelChoiceType::class, [
                'placeholder' => 'setono_sylius_restock_notification.form.restock_notification_request.channel_placeholder',
                'label' => 'sylius.ui.channel',
            ])
            ->add('locale', LocaleChoiceType::class, [
                'label' => 'sylius.ui.locale',
            ])
            ->add('email', EmailType::class, [
                'label' => 'sylius.ui.email',
            ])
        ;
    }
}
