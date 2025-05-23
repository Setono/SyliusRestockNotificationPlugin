<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Tests\Message\Handler;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusRestockNotificationPlugin\Message\Command\Notify;
use Setono\SyliusRestockNotificationPlugin\Message\Event\ProductVariantRestocked;
use Setono\SyliusRestockNotificationPlugin\Message\Handler\ProductVariantRestockedHandler;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Setono\SyliusRestockNotificationPlugin\Repository\RestockNotificationRequestRepositoryInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductVariantRestockedHandlerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<ProductVariantRepositoryInterface> */
    private ObjectProphecy $productVariantRepository;

    /** @var ObjectProphecy<RestockNotificationRequestRepositoryInterface> */
    private ObjectProphecy $restockNotificationRequestRepository;

    /** @var ObjectProphecy<MessageBusInterface> */
    private ObjectProphecy $commandBus;

    protected function setUp(): void
    {
        $this->productVariantRepository = $this->prophesize(ProductVariantRepositoryInterface::class);
        $this->restockNotificationRequestRepository = $this->prophesize(RestockNotificationRequestRepositoryInterface::class);
        $this->commandBus = $this->prophesize(MessageBusInterface::class);
    }

    /**
     * @test
     */
    public function it_does_nothing_when_product_variant_is_not_found(): void
    {
        $handler = new ProductVariantRestockedHandler(
            $this->productVariantRepository->reveal(),
            $this->restockNotificationRequestRepository->reveal(),
            $this->commandBus->reveal(),
        );

        $productVariantId = 123;
        $message = new ProductVariantRestocked($productVariantId);

        $this->productVariantRepository->find($productVariantId)->willReturn(null);

        $handler($message);

        // Assert that findByProductVariant was not called on the repository
        $this->restockNotificationRequestRepository->findByProductVariant(\Prophecy\Argument::any())->shouldNotHaveBeenCalled();

        // Assert that dispatch was not called on the command bus
        $this->commandBus->dispatch(\Prophecy\Argument::any())->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     */
    public function it_dispatches_notify_commands_for_each_restock_notification_request(): void
    {
        $handler = new ProductVariantRestockedHandler(
            $this->productVariantRepository->reveal(),
            $this->restockNotificationRequestRepository->reveal(),
            $this->commandBus->reveal(),
        );

        $productVariantId = 123;
        $message = new ProductVariantRestocked($productVariantId);

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $this->productVariantRepository->find($productVariantId)->willReturn($productVariant->reveal());

        $request1 = $this->prophesize(RestockNotificationRequestInterface::class);
        $request2 = $this->prophesize(RestockNotificationRequestInterface::class);

        $this->restockNotificationRequestRepository->findByProductVariant($productVariant->reveal())
            ->willReturn([$request1->reveal(), $request2->reveal()]);

        // Mock the dispatch method to return an envelope
        $this->commandBus->dispatch(new Notify($request1->reveal()))
            ->willReturn(new Envelope(new Notify($request1->reveal())));
        $this->commandBus->dispatch(new Notify($request2->reveal()))
            ->willReturn(new Envelope(new Notify($request2->reveal())));

        $handler($message);

        // Assert that dispatch was called twice on the command bus
        $this->commandBus->dispatch(\Prophecy\Argument::type(Notify::class))->shouldHaveBeenCalledTimes(2);
    }
}
