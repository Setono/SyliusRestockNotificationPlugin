# Sylius Restock Notification Plugin

[![Build Status][ico-github-actions]][link-github-actions]

This plugin allows customers to sign up for notifications when a product is back in stock.

## Installation

### Step 1: Download the plugin

This is a private plugin, so you need to add a custom repository to your `composer.json`:

```bash
$ composer config repositories.setono-sylius-restock-notification vcs git@github.com:Setono/SyliusRestockNotificationPlugin.git
$ composer require setono/sylius-restock-notification-plugin
```

### Step 2: Enable the plugin

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

### Step 3: Configure plugin

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

### Step 4: Add template
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
$ php bin/console doctrine:migrations:diff
$ php bin/console doctrine:migrations:migrate
```

[ico-github-actions]: https://github.com/Setono/SyliusRestockNotificationPlugin/workflows/build/badge.svg
[link-github-actions]: https://github.com/Setono/SyliusRestockNotificationPlugin/actions
