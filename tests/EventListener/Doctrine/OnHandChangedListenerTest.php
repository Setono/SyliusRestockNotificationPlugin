<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Tests\EventListener\Doctrine;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusRestockNotificationPlugin\EventListener\Doctrine\OnHandChangedListener;
use Setono\SyliusRestockNotificationPlugin\Message\Event\ProductVariantRestocked;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OnHandChangedListenerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<MessageBusInterface> */
    private ObjectProphecy $eventBus;

    protected function setUp(): void
    {
        $this->eventBus = $this->prophesize(MessageBusInterface::class);
    }

    /**
     * @test
     */
    public function it_does_nothing_when_object_is_not_a_product_variant_in_preupdate(): void
    {
        $this->eventBus->dispatch(\Prophecy\Argument::cetera())
            ->shouldNotBeCalled();

        $listener = new OnHandChangedListener($this->eventBus->reveal());

        $eventArgs = $this->prophesize(PreUpdateEventArgs::class);
        $eventArgs->getObject()->willReturn(new \stdClass());

        $listener->preUpdate($eventArgs->reveal());
    }

    /**
     * @test
     */
    public function it_does_nothing_when_onhand_and_onhold_fields_are_not_changed(): void
    {
        $this->eventBus->dispatch(\Prophecy\Argument::cetera())
            ->shouldNotBeCalled();

        $listener = new OnHandChangedListener($this->eventBus->reveal());

        $productVariant = $this->prophesize(ProductVariantInterface::class);

        $eventArgs = $this->prophesize(PreUpdateEventArgs::class);
        $eventArgs->getObject()->willReturn($productVariant->reveal());
        $eventArgs->hasChangedField('onHand')->willReturn(false);
        $eventArgs->hasChangedField('onHold')->willReturn(false);

        $listener->preUpdate($eventArgs->reveal());
    }

    /**
     * @test
     */
    public function it_does_nothing_when_old_stock_is_greater_than_zero(): void
    {
        $this->eventBus->dispatch(\Prophecy\Argument::cetera())
            ->shouldNotBeCalled();

        $listener = new OnHandChangedListener($this->eventBus->reveal());

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getOnHand()->willReturn(10);
        $productVariant->getOnHold()->willReturn(5);

        $eventArgs = $this->prophesize(PreUpdateEventArgs::class);
        $eventArgs->getObject()->willReturn($productVariant->reveal());
        $eventArgs->hasChangedField('onHand')->willReturn(true);
        $eventArgs->hasChangedField('onHold')->willReturn(false);
        $eventArgs->getOldValue('onHand')->willReturn(10);
        $eventArgs->getNewValue('onHand')->willReturn(15);

        $listener->preUpdate($eventArgs->reveal());
    }

    /**
     * @test
     */
    public function it_does_nothing_when_new_stock_is_not_greater_than_zero(): void
    {
        $this->eventBus->dispatch(\Prophecy\Argument::cetera())
            ->shouldNotBeCalled();

        $listener = new OnHandChangedListener($this->eventBus->reveal());

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getOnHand()->willReturn(5);
        $productVariant->getOnHold()->willReturn(5);

        $eventArgs = $this->prophesize(PreUpdateEventArgs::class);
        $eventArgs->getObject()->willReturn($productVariant->reveal());
        $eventArgs->hasChangedField('onHand')->willReturn(true);
        $eventArgs->hasChangedField('onHold')->willReturn(false);
        $eventArgs->getOldValue('onHand')->willReturn(0);
        $eventArgs->getNewValue('onHand')->willReturn(5);

        $listener->preUpdate($eventArgs->reveal());
    }

    /**
     * @test
     */
    public function it_adds_product_variant_to_candidates_when_restocked(): void
    {
        $listener = new OnHandChangedListener($this->eventBus->reveal());

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getOnHand()->willReturn(10);
        $productVariant->getOnHold()->willReturn(5);
        $productVariant->getId()->willReturn(123);

        $eventArgs = $this->prophesize(PreUpdateEventArgs::class);
        $eventArgs->getObject()->willReturn($productVariant->reveal());
        $eventArgs->hasChangedField('onHand')->willReturn(true);
        $eventArgs->hasChangedField('onHold')->willReturn(false);
        $eventArgs->getOldValue('onHand')->willReturn(0);
        $eventArgs->getNewValue('onHand')->willReturn(10);

        $listener->preUpdate($eventArgs->reveal());

        // Now test postUpdate to verify the candidate is processed
        $lifecycleEventArgs = $this->prophesize(LifecycleEventArgs::class);
        $lifecycleEventArgs->getObject()->willReturn($productVariant->reveal());

        $this->eventBus->dispatch(new ProductVariantRestocked(123))
            ->shouldBeCalled()
            ->willReturn(new Envelope(new ProductVariantRestocked(123)));

        $listener->postUpdate($lifecycleEventArgs->reveal());
    }

    /**
     * @test
     */
    public function it_does_nothing_when_object_is_not_a_product_variant_in_postupdate(): void
    {
        $this->eventBus->dispatch(\Prophecy\Argument::cetera())
            ->shouldNotBeCalled();

        $listener = new OnHandChangedListener($this->eventBus->reveal());

        $lifecycleEventArgs = $this->prophesize(LifecycleEventArgs::class);
        $lifecycleEventArgs->getObject()->willReturn(new \stdClass());

        $listener->postUpdate($lifecycleEventArgs->reveal());
    }

    /**
     * @test
     */
    public function it_does_nothing_when_product_variant_is_not_in_candidates(): void
    {
        $listener = new OnHandChangedListener($this->eventBus->reveal());

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getId()->willReturn(123);

        $lifecycleEventArgs = $this->prophesize(LifecycleEventArgs::class);
        $lifecycleEventArgs->getObject()->willReturn($productVariant->reveal());

        $this->eventBus->dispatch(new ProductVariantRestocked(123))
            ->shouldNotBeCalled();

        $listener->postUpdate($lifecycleEventArgs->reveal());
    }

    /**
     * @test
     */
    public function it_resets_candidates(): void
    {
        $listener = new OnHandChangedListener($this->eventBus->reveal());

        // First add a candidate
        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getOnHand()->willReturn(10);
        $productVariant->getOnHold()->willReturn(5);
        $productVariant->getId()->willReturn(123);

        $eventArgs = $this->prophesize(PreUpdateEventArgs::class);
        $eventArgs->getObject()->willReturn($productVariant->reveal());
        $eventArgs->hasChangedField('onHand')->willReturn(true);
        $eventArgs->hasChangedField('onHold')->willReturn(false);
        $eventArgs->getOldValue('onHand')->willReturn(0);
        $eventArgs->getNewValue('onHand')->willReturn(10);

        $listener->preUpdate($eventArgs->reveal());

        // Now reset
        $listener->reset();

        // Verify the candidate was reset by checking that postUpdate doesn't dispatch an event
        $lifecycleEventArgs = $this->prophesize(LifecycleEventArgs::class);
        $lifecycleEventArgs->getObject()->willReturn($productVariant->reveal());

        $this->eventBus->dispatch(new ProductVariantRestocked(123))
            ->shouldNotBeCalled();

        $listener->postUpdate($lifecycleEventArgs->reveal());
    }
}
