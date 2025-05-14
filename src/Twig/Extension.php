<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class Extension extends AbstractExtension
{
    public function getFunctions(): array
    {
        /** @psalm-suppress InvalidArgument */
        return [
            new TwigFunction(
                'ssrn_resources',
                [Runtime::class, 'resources'],
                ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['html']],
            ),
        ];
    }
}
