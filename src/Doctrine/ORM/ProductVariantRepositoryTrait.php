<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Doctrine\ORM;

use function assert;
use Doctrine\ORM\EntityRepository;

/**
 * @mixin EntityRepository
 */
trait ProductVariantRepositoryTrait
{
    public function findByPhrase(string $phrase, string $locale): array
    {
        assert($this instanceof EntityRepository);

        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.product', 'product')
            ->orWhere('product.code LIKE :phrase')
            ->orWhere('translation.name LIKE :phrase')
            ->orWhere('o.code LIKE :phrase')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult()
        ;
    }
}
