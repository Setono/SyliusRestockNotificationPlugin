<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('setono_sylius_restock_notification');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
