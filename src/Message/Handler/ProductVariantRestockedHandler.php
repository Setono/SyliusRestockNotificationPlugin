<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Message\Handler;

use Setono\SyliusRestockNotificationPlugin\Message\Command\Notify;
use Setono\SyliusRestockNotificationPlugin\Message\Event\ProductVariantRestocked;
use Setono\SyliusRestockNotificationPlugin\Repository\NotificationRepositoryInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductVariantRestockedHandler implements MessageHandlerInterface
{
    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var NotificationRepositoryInterface */
    private $notificationRepository;

    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        NotificationRepositoryInterface $notificationRepository,
        MessageBusInterface $commandBus
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->notificationRepository = $notificationRepository;
        $this->commandBus = $commandBus;
    }

    public function __invoke(ProductVariantRestocked $message): void
    {
        $productVariant = $this->productVariantRepository->find($message->getProductVariantId());

        if (null === $productVariant || !$productVariant instanceof ProductVariantInterface) {
            return;
        }

        $notifications = $this->notificationRepository->findByProductVariant($productVariant);

        foreach ($notifications as $notification) {
            $this->commandBus->dispatch(new Notify($notification->getId()));
        }
    }
}
