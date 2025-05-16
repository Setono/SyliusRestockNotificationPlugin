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

### Configure plugin

Add this file to your `config/packages` directory:
```yaml
# config/packages/setono_sylius_restock_notification.yaml
imports:
    - { resource: "@SetonoSyliusRestockNotificationPlugin/Resources/config/app/config.yaml" }
```

Add this file to your `config/routes` directory:
```yaml
# config/routes/setono_sylius_restock_notification.yaml
setono_sylius_restock_notification:
    resource: "@SetonoSyliusRestockNotificationPlugin/Resources/config/routes.yaml"
```

### Add template
If you use the new Sylius UI template event system, you can add the template like this:

```yaml
# config/packages/setono_sylius_restock_notification.yaml
sylius_ui:
    events:
        sylius.shop.product.show.right_sidebar:
            blocks:
                setono_sylius_restock_notification_available_notifications: "@SetonoSyliusRestockNotificationPlugin/shop/notification/available.html.twig"
```

Notice that the event name that is used is `sylius.shop.product.show.right_sidebar`. If you want to add it to another
event, you should replace that line with your event name.

If you don't use the Sylius UI template event system, you can just include the template
`@SetonoSyliusRestockNotificationPlugin/shop/notification/available.html.twig` as you normally would.

### Step 5: Update your database schema

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### Configure Symfony Messenger (optional, but recommended)
This plugin uses Symfony messenger to notify the customer when products are restocked. This can be done asynchronously
using this configuration:

```yaml
framework:
    messenger:
        routing:
            # Route all command messages to the async transport
            # This presumes that you have already set up an 'async' transport
            'Setono\SyliusRestockNotificationPlugin\Message\Command\CommandInterface': async
```

[ico-version]: https://poser.pugx.org/setono/sylius-restock-notification-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-restock-notification-plugin/license
[ico-github-actions]: https://github.com/Setono/sylius-restock-notification-plugin/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/sylius-restock-notification-plugin/branch/master/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2Fsylius-restock-notification-plugin%2Fmaster

[link-packagist]: https://packagist.org/packages/setono/sylius-restock-notification-plugin
[link-github-actions]: https://github.com/Setono/sylius-restock-notification-plugin/actions
[link-code-coverage]: https://codecov.io/gh/Setono/sylius-restock-notification-plugin
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/sylius-restock-notification-plugin/master
