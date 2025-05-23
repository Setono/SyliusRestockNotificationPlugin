# Sylius Restock Notification Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

This plugin allows customers to sign up for notifications when a product is back in stock.

## Installation

```bash
composer require setono/sylius-restock-notification-plugin
```

### Enable the plugin

Then, enable the plugin by adding it to the list of registered plugins/bundles
in the `config/bundles.php` file of your project before (!) `SyliusGridBundle` and the `FrameworkBundle`:

```php
<?php
# config/bundles.php
return [
    Setono\SyliusRestockNotificationPlugin\SetonoSyliusRestockNotificationPlugin::class => ['all' => true],
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    
    // ...
    
    Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
];
```

### Import routes

Add this file to your `config/routes` directory:
```yaml
# config/routes/setono_sylius_restock_notification.yaml
setono_sylius_restock_notification:
    resource: "@SetonoSyliusRestockNotificationPlugin/Resources/config/routes.yaml"
```

### Update your database schema

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

[ico-version]: https://poser.pugx.org/setono/sylius-restock-notification-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-restock-notification-plugin/license
[ico-github-actions]: https://github.com/Setono/SyliusRestockNotificationPlugin/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/SyliusRestockNotificationPlugin/branch/master/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2FSyliusRestockNotificationPlugin%2Fmaster

[link-packagist]: https://packagist.org/packages/setono/sylius-restock-notification-plugin
[link-github-actions]: https://github.com/Setono/SyliusRestockNotificationPlugin/actions
[link-code-coverage]: https://codecov.io/gh/Setono/SyliusRestockNotificationPlugin
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/SyliusRestockNotificationPlugin/master
