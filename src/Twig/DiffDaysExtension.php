<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Twig;

use DateTimeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class DiffDaysExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('setono_restock_notification_diff_days', $this->diffDays(...)),
        ];
    }

    public function diffDays(DateTimeInterface $from, ?DateTimeInterface $to): int
    {
        if (null === $to) {
            $to = new \DateTimeImmutable();
        }

        return (int) $from->diff($to, true)->format('%a') + 1;
    }
}
