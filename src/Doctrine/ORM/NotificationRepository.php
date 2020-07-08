<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Doctrine\ORM;

use Setono\SyliusRestockNotificationPlugin\Model\NotificationInterface;
use Setono\SyliusRestockNotificationPlugin\Repository\NotificationRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Model\ProductVariantInterface;

class NotificationRepository extends EntityRepository implements NotificationRepositoryInterface
{
    public function findOneByIdInState(int $id, string $state): ?NotificationInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->andWhere('o.state = :state')
            ->setParameters([
                'id' => $id,
                'state' => $state,
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByProductVariant(ProductVariantInterface $productVariant): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.productVariant = :variant')
            ->setParameters([
                'variant' => $productVariant,
            ])
            ->getQuery()
            ->getResult()
        ;
    }

    public function hasNotification(NotificationInterface $notification): bool
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->andWhere('o.productVariant = :variant')
            ->andWhere('o.email = :email')
            ->setParameters([
                'variant' => $notification->getProductVariant(),
                'email' => $notification->getEmail(),
            ])
            ->getQuery()
            ->getSingleScalarResult() > 0
        ;
    }
}
