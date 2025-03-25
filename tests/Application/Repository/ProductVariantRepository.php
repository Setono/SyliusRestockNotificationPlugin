<?php
declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Tests\Application\Repository;

use Setono\SyliusRestockNotificationPlugin\Doctrine\ORM\ProductVariantRepositoryTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository as BaseProductVariantRepository;

final class ProductVariantRepository extends BaseProductVariantRepository
{
    use ProductVariantRepositoryTrait;
}
