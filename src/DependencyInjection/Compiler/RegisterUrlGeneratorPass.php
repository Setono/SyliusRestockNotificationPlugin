<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\DependencyInjection\Compiler;

use Setono\SyliusRestockNotificationPlugin\UrlGenerator\ProductUrlGenerator;
use Setono\SyliusRestockNotificationPlugin\UrlGenerator\ProductVariantUrlGenerator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * If the user has installed and enabled the variant link plugin (https://github.com/Setono/SyliusVariantLinkPlugin)
 * we will automatically use the ProductVariantUrlGenerator instead of the ProductUrlGenerator
 */
final class RegisterUrlGeneratorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (array_key_exists('SetonoSyliusVariantLinkPlugin', $bundles) &&
            $container->has('setono_sylius_variant_link.url_generator.product_variant.default')
        ) {
            $definition = $this->getProductVariantUrlGeneratorDefinition();
        } else {
            $definition = $this->getProductUrlGeneratorDefinition();
        }

        $container->setDefinition('setono_sylius_restock_notification.url_generator.product_variant', $definition);
    }

    private function getProductUrlGeneratorDefinition(): Definition
    {
        return new Definition(ProductUrlGenerator::class, [
            new Reference('router'),
        ]);
    }

    private function getProductVariantUrlGeneratorDefinition(): Definition
    {
        return new Definition(ProductVariantUrlGenerator::class, [
            new Reference('setono_sylius_variant_link.url_generator.product_variant.default'),
        ]);
    }
}
