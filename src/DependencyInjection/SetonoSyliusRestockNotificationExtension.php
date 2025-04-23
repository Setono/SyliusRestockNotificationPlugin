<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\DependencyInjection;

use Setono\SyliusRestockNotificationPlugin\Mailer\Emails;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
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

        $this->registerResources(
            'setono_sylius_restock_notification',
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            $config['resources'],
            $container,
        );

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

        $container->prependExtensionConfig('sylius_grid', [
            'grids' => [
                'setono_sylius_restock_notification_admin_restock_notification_request' => [
                    'driver' => [
                        'name' => SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
                        'options' => [
                            'class' => '%setono_sylius_restock_notification.model.restock_notification_request.class%',
                        ],
                    ],
                    'sorting' => [
                        'createdAt' => 'desc',
                    ],
                    'fields' => [
                        'email' => [
                            'type' => 'string',
                            'label' => 'sylius.ui.email',
                        ],
                        'product_variant' => [
                            'type' => 'twig',
                            'label' => 'sylius.ui.variant',
                            'path' => '.',
                            'options' => [
                                'template' => '@SetonoSyliusRestockNotificationPlugin/admin/grid/field/product_variant.html.twig',
                            ],
                        ],
                        'state' => [
                            'type' => 'twig',
                            'label' => 'sylius.ui.state',
                            'options' => [
                                'template' => '@SyliusUi/Grid/Field/state.html.twig',
                                'vars' => [
                                    'labels' => '@SetonoSyliusRestockNotificationPlugin/admin/grid/field/state',
                                ],
                            ],
                        ],
                        'createdAt' => [
                            'type' => 'datetime',
                            'label' => 'sylius.ui.created_at',
                            'sortable' => null,
                        ],
                        'sentAt' => [
                            'type' => 'datetime',
                            'label' => 'setono_sylius_restock_notification.ui.sent_at',
                            'sortable' => null,
                        ],
                        'daysWaited' => [
                            'type' => 'twig',
                            'path' => '.',
                            'label' => 'setono_sylius_restock_notification.ui.days_waited',
                            'options' => [
                                'template' => '@SetonoSyliusRestockNotificationPlugin/admin/grid/field/days_waited.html.twig',
                            ],
                        ],
                    ],
                    'filters' => [
                        'email' => [
                            'type' => 'string',
                            'label' => 'sylius.ui.email',
                            'options' => [
                                'fields' => ['email'],
                            ],
                        ],
                        'state' => [
                            'type' => 'select',
                            'label' => 'sylius.ui.state',
                            'form_options' => [
                                'choices' => [
                                    'setono_sylius_restock_restock_notification_request.ui.states.error' => RestockNotificationRequestInterface::STATE_FAILED,
                                    'setono_sylius_restock_restock_notification_request.ui.states.pending' => RestockNotificationRequestInterface::STATE_PENDING,
                                    'setono_sylius_restock_restock_notification_request.ui.states.processing' => RestockNotificationRequestInterface::STATE_PROCESSING,
                                    'setono_sylius_restock_restock_notification_request.ui.states.sent' => RestockNotificationRequestInterface::STATE_SENT,
                                ],
                            ],
                        ],
                    ],
                    'actions' => [
                        'main' => [
                            'create' => [
                                'type' => 'create',
                            ],
                        ],
                        'item' => [
                            'update' => [
                                'type' => 'update',
                            ],
                            'delete' => [
                                'type' => 'delete',
                            ],
                        ],
                        'bulk' => [
                            'delete' => [
                                'type' => 'delete',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
