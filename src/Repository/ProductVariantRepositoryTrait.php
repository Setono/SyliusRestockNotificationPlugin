<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @mixin EntityRepository
 */
trait ProductVariantRepositoryTrait
{
    public function findByPhraseWithoutLocale(string $phrase): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->innerJoin('o.product', 'product')
            ->innerJoin('product.translations', 'pTranslation')
            ->orWhere('product.code LIKE :phrase')
            ->orWhere('translation.name LIKE :phrase')
            ->orWhere('pTranslation.name LIKE :phrase')
            ->orWhere('o.code LIKE :phrase')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->getQuery()
            ->getResult()
        ;
    }
}
