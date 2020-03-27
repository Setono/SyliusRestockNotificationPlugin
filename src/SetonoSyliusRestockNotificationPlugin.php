<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin;

use Setono\SyliusRestockNotificationPlugin\DependencyInjection\Compiler\RegisterUrlGeneratorPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetonoSyliusRestockNotificationPlugin extends AbstractResourceBundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterUrlGeneratorPass());
    }

    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }
}
