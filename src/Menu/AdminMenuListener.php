<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $marketingSubmenu = $menu->getChild('marketing');
        if (!$marketingSubmenu instanceof ItemInterface) {
            return;
        }

        $marketingSubmenu
            ->addChild('restock_notifications', [
                'route' => 'setono_sylius_restock_notification_admin_restock_notification_request_index',
            ])
            ->setAttribute('type', 'link')
            ->setLabel('setono_sylius_restock_notification.menu.admin.main.marketing.restock_notification_requests')
            ->setLabelAttributes([
                'icon' => 'redo',
            ])
        ;
    }
}
