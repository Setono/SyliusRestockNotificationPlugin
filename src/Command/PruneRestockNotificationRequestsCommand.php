<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Command;

use DateTimeImmutable;
use Setono\SyliusRestockNotificationPlugin\Repository\RestockNotificationRequestRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'setono:sylius-restock-notification:prune-requests',
    description: 'Remove restock notification requests older than the configured threshold',
)]
final class PruneRestockNotificationRequestsCommand extends Command
{
    public function __construct(
        private readonly RestockNotificationRequestRepositoryInterface $restockNotificationRequestRepository,
        private readonly int $pruningThreshold,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'pruning-threshold',
                null,
                InputOption::VALUE_REQUIRED,
                'Override the configured threshold (in days)',
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $pruningThreshold = (int) ($input->getOption('pruning-threshold') ?? $this->pruningThreshold);
        $date = new DateTimeImmutable(sprintf('-%d days', $pruningThreshold));

        $count = $this->restockNotificationRequestRepository->removeOlderThan($date);

        $io->success(sprintf(
            'Removed %d restock notification request(s) older than %s',
            $count,
            $date->format('Y-m-d H:i:s'),
        ));

        return Command::SUCCESS;
    }
}
