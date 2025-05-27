<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Tests\Command;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusRestockNotificationPlugin\Command\PruneRestockNotificationRequestsCommand;
use Setono\SyliusRestockNotificationPlugin\Repository\RestockNotificationRequestRepositoryInterface;
use Symfony\Component\Console\Tester\CommandTester;

final class PruneRestockNotificationRequestsCommandTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<RestockNotificationRequestRepositoryInterface> */
    private ObjectProphecy $repository;

    private int $thresholdDays = 90;

    protected function setUp(): void
    {
        $this->repository = $this->prophesize(RestockNotificationRequestRepositoryInterface::class);
    }

    /**
     * @test
     */
    public function it_removes_old_restock_notification_requests(): void
    {
        $command = new PruneRestockNotificationRequestsCommand(
            $this->repository->reveal(),
            $this->thresholdDays,
        );

        $commandTester = new CommandTester($command);

        // Mock the repository to return a count of 5 removed requests
        $this->repository->removeOlderThan(Argument::type(DateTimeImmutable::class))
            ->willReturn(5)
            ->shouldBeCalled();

        $exitCode = $commandTester->execute([]);

        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('Removed 5 restock notification request(s)', $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function it_uses_custom_threshold_when_provided(): void
    {
        $command = new PruneRestockNotificationRequestsCommand(
            $this->repository->reveal(),
            $this->thresholdDays,
        );

        $commandTester = new CommandTester($command);

        // Mock the repository to return a count of 3 removed requests
        // The date should be 30 days ago, not the default 90
        $this->repository->removeOlderThan(Argument::that(function (DateTimeImmutable $date) {
            $expectedDate = new DateTimeImmutable('-30 days');
            // Allow for a small difference due to execution time
            $diff = abs($expectedDate->getTimestamp() - $date->getTimestamp());

            return $diff < 5; // Allow 5 seconds difference
        }))->willReturn(3)->shouldBeCalled();

        $exitCode = $commandTester->execute([
            '--pruning-threshold' => 30,
        ]);

        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('Removed 3 restock notification request(s)', $commandTester->getDisplay());
    }
}
