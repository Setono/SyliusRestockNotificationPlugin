<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Tests\Message\Handler;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusRestockNotificationPlugin\Message\Command\Notify;
use Setono\SyliusRestockNotificationPlugin\Message\Handler\NotifyHandler;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Setono\SyliusRestockNotificationPlugin\Notifier\NotifierInterface;
use Setono\SyliusRestockNotificationPlugin\Repository\RestockNotificationRequestRepositoryInterface;

final class NotifyHandlerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<RestockNotificationRequestRepositoryInterface> */
    private ObjectProphecy $repository;

    /** @var ObjectProphecy<NotifierInterface> */
    private ObjectProphecy $notifier;

    protected function setUp(): void
    {
        $this->repository = $this->prophesize(RestockNotificationRequestRepositoryInterface::class);
        $this->notifier = $this->prophesize(NotifierInterface::class);
    }

    /**
     * @test
     */
    public function it_does_nothing_when_restock_notification_request_is_not_found(): void
    {
        $handler = new NotifyHandler(
            $this->repository->reveal(),
            $this->notifier->reveal(),
        );

        $requestId = 123;
        $message = new Notify($requestId);

        $this->repository->findOneByIdInState($requestId, RestockNotificationRequestInterface::STATE_PENDING)
            ->willReturn(null);

        $handler($message);

        // Assert that notify was not called on the notifier
        $this->notifier->notify(\Prophecy\Argument::any())->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     */
    public function it_notifies_when_restock_notification_request_is_found(): void
    {
        $handler = new NotifyHandler(
            $this->repository->reveal(),
            $this->notifier->reveal(),
        );

        $requestId = 123;
        $message = new Notify($requestId);

        $request = $this->prophesize(RestockNotificationRequestInterface::class);

        $this->repository->findOneByIdInState($requestId, RestockNotificationRequestInterface::STATE_PENDING)
            ->willReturn($request->reveal());

        $handler($message);

        // Assert that notify was called on the notifier with the request
        $this->notifier->notify($request->reveal())->shouldHaveBeenCalled();
    }
}
