<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

final class RestockNotificationAdminRequestType extends AbstractResourceType
{
    /**
     * @param class-string $dataClass
     * @param list<string> $validationGroups
     */
    public function __construct(
        private readonly RepositoryInterface $localeRepository,
        string $dataClass,
        array $validationGroups = [],
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('productVariant', ProductVariantAutocompleteChoiceType::class, [
                'label' => 'sylius.ui.variant',
            ])
            ->add('channel', ChannelChoiceType::class, [
                'placeholder' => 'setono_sylius_restock_notification.form.restock_notification_request.channel_placeholder',
                'label' => 'sylius.ui.channel',
            ])
            ->add('localeCode', LocaleChoiceType::class, [
                'label' => 'sylius.ui.locale',
            ])
            ->add('email', EmailType::class, [
                'label' => 'sylius.ui.email',
            ]);

        $builder->get('localeCode')->addModelTransformer(
            new ReversedTransformer(new ResourceToIdentifierTransformer($this->localeRepository, 'code')),
        );
    }
}
