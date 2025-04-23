<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\DependencyInjection;

use Setono\SyliusRestockNotificationPlugin\Mailer\Emails;
use Setono\SyliusRestockNotificationPlugin\Workflow\RestockNotificationRequestWorkflow;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusRestockNotificationExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         *
         * @var array{resources: array} $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('setono_sylius_restock_notification', SyliusResourceBundle::DRIVER_DOCTRINE_ORM, $config['resources'], $container);

        $loader->load('services.xml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('framework', [
            'workflows' => RestockNotificationRequestWorkflow::getConfig(),
            'messenger' => [
                'buses' => [
                    'setono_sylius_restock_notification.command_bus' => null,
                    'setono_sylius_restock_notification.event_bus' => [
                        'default_middleware' => 'allow_no_handlers',
                    ],
                ],
            ],
        ]);

        $container->prependExtensionConfig('twig', [
            'form_themes' => [
                '@SetonoSyliusRestockNotificationPlugin/shop/form/theme.html.twig',
            ],
        ]);

        $container->prependExtensionConfig('sylius_mailer', [
            'emails' => [
                Emails::RESTOCK_NOTIFICATION_REQUEST => [
                    'subject' => 'setono_sylius_restock_notification.emails.restock_notification_request.subject',
                    'template' => '@SetonoSyliusRestockNotificationPlugin/email/restock_notification_request.html.twig',
                ],
            ],
        ]);
    }
}
