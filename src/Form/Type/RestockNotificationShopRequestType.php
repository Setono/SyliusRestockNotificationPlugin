<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Form\Type;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

final class RestockNotificationShopRequestType extends RestockNotificationRequestType
{
    /**
     * @param class-string $dataClass
     * @param list<string> $validationGroups
     */
    public function __construct(
        private readonly ChannelContextInterface $channelContext,
        private readonly LocaleContextInterface $localeContext,
        string $dataClass,
        array $validationGroups = [],
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('productVariant', OutOfStockProductVariantChoiceType::class, [
                'label' => 'setono_sylius_restock_notification.form.notification.product_variant',
                'product' => $options['product'],
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (PreSubmitEvent $event): void {
                /** @var mixed $data */
                $data = $event->getData();
                Assert::isArray($data);

                $data['channel'] = $this->channelContext->getChannel()->getCode();
                $data['locale'] = $this->localeContext->getLocaleCode();

                $event->setData($data);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver
            ->setRequired(['product'])
            ->setAllowedTypes('product', ProductInterface::class);
    }
}
