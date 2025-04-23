<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Message\Handler;

use Setono\SyliusRestockNotificationPlugin\Message\Command\Notify;
use Setono\SyliusRestockNotificationPlugin\Message\Event\ProductVariantRestocked;
use Setono\SyliusRestockNotificationPlugin\Repository\RestockNotificationRequestRepositoryInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductVariantRestockedHandler
{
    public function __construct(
        private readonly ProductVariantRepositoryInterface $productVariantRepository,
        private readonly RestockNotificationRequestRepositoryInterface $restockNotificationRequestRepository,
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(ProductVariantRestocked $message): void
    {
        $productVariant = $this->productVariantRepository->find($message->productVariant);

        if (!$productVariant instanceof ProductVariantInterface) {
            return;
        }

        $restockNotificationRequests = $this->restockNotificationRequestRepository->findByProductVariant($productVariant);

        foreach ($restockNotificationRequests as $restockNotificationRequest) {
            $this->commandBus->dispatch(new Notify($restockNotificationRequest));
        }
    }
}
