<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\DependencyInjection;

use Setono\SyliusRestockNotificationPlugin\Doctrine\ORM\NotificationRepository;
use Setono\SyliusRestockNotificationPlugin\Form\Type\NotificationType;
use Setono\SyliusRestockNotificationPlugin\Model\Notification;
use Setono\SyliusRestockNotificationPlugin\Model\NotificationInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('setono_sylius_restock_notification');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
            ->arrayNode('resources')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('notification')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->variableNode('options')->end()
                            ->arrayNode('classes')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('model')->defaultValue(Notification::class)->cannotBeEmpty()->end()
                                    ->scalarNode('interface')->defaultValue(NotificationInterface::class)->cannotBeEmpty()->end()
                                    ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                    ->scalarNode('repository')->defaultValue(NotificationRepository::class)->cannotBeEmpty()->end()
                                    ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                    ->scalarNode('form')->defaultValue(NotificationType::class)->cannotBeEmpty()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
