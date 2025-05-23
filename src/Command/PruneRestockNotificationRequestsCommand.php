<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Command;

use DateTimeImmutable;
use Setono\SyliusRestockNotificationPlugin\Repository\RestockNotificationRequestRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PruneRestockNotificationRequestsCommand extends Command
{
    protected static $defaultName = 'setono:sylius-restock-notification:prune-requests';

    protected static $defaultDescription = 'Remove restock notification requests older than the configured threshold';

    private RestockNotificationRequestRepositoryInterface $restockNotificationRequestRepository;

    private int $pruningThreshold;

    public function __construct(
        RestockNotificationRequestRepositoryInterface $restockNotificationRequestRepository,
        int $pruningThreshold,
    ) {
        parent::__construct();

        $this->restockNotificationRequestRepository = $restockNotificationRequestRepository;
        $this->pruningThreshold = $pruningThreshold;
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'threshold-days',
                't',
                InputOption::VALUE_REQUIRED,
                'Override the configured threshold (in days)',
                null,
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $thresholdDays = (int) ($input->getOption('threshold-days') ?? $this->pruningThreshold);
        $date = new DateTimeImmutable(sprintf('-%d days', $thresholdDays));

        $count = $this->restockNotificationRequestRepository->removeOlderThan($date);

        $io->success(sprintf(
            'Removed %d restock notification request(s) older than %s',
            $count,
            $date->format('Y-m-d H:i:s'),
        ));

        return Command::SUCCESS;
    }
}
