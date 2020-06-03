<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusRestockNotificationPlugin\Model\NotificationInterface;
use Setono\SyliusRestockNotificationPlugin\Repository\NotificationRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Model\ProductVariantInterface;

class NotificationRepository extends EntityRepository implements NotificationRepositoryInterface
{
    public function createGroupByProductListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->select('NEW \Setono\SyliusRestockNotificationPlugin\Model\NotificationGroup(variant.code, COUNT(o.productVariant))')
            ->join('o.productVariant', 'variant')
            ->groupBy('o.productVariant')
            ->addOrderBy('COUNT(o.productVariant)', 'desc')
        ;
    }

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
}
