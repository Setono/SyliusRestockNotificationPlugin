<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Doctrine\ORM;

use Setono\SyliusRestockNotificationPlugin\Model\NotificationInterface;
use Setono\SyliusRestockNotificationPlugin\Repository\NotificationRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

class NotificationRepository extends EntityRepository implements NotificationRepositoryInterface
{
    public function findOneByIdInState(int $id, string $state): ?NotificationInterface
    {
        $obj = $this->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->andWhere('o.state = :state')
            ->setParameters([
                'id' => $id,
                'state' => $state,
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;

        Assert::nullOrIsInstanceOf($obj, NotificationInterface::class);

        return $obj;
    }

    public function findByProductVariant(ProductVariantInterface $productVariant): array
    {
        $objs = $this->createQueryBuilder('o')
            ->andWhere('o.productVariant = :variant')
            ->setParameters([
                'variant' => $productVariant,
            ])
            ->getQuery()
            ->getResult()
        ;

        Assert::isArray($objs);
        Assert::allIsInstanceOf($objs, NotificationInterface::class);

        return $objs;
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
