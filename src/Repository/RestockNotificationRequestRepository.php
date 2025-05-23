<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Repository;

use DateTimeInterface;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

class RestockNotificationRequestRepository extends EntityRepository implements RestockNotificationRequestRepositoryInterface
{
    public function findOneByIdInState(int $id, string $state): ?RestockNotificationRequestInterface
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

        Assert::nullOrIsInstanceOf($obj, RestockNotificationRequestInterface::class);

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
        Assert::allIsInstanceOf($objs, RestockNotificationRequestInterface::class);

        return $objs;
    }

    public function hasRestockNotificationRequest(RestockNotificationRequestInterface $notification): bool
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

    public function removeOlderThan(DateTimeInterface $date): int
    {
        return (int) $this->createQueryBuilder('o')
            ->delete()
            ->andWhere('o.createdAt < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->execute()
        ;
    }
}
