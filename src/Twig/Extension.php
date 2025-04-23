<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class Extension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ssrn_product_page',
                [Runtime::class, 'productPage'],
                ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['html']],
            ),
        ];
    }
}
