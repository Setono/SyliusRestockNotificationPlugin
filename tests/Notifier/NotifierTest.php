<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Tests\Notifier;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusRestockNotificationPlugin\EmailManager\RestockNotificationEmailManagerInterface;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Setono\SyliusRestockNotificationPlugin\Notifier\Notifier;
use Setono\SyliusRestockNotificationPlugin\Workflow\RestockNotificationRequestWorkflow;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\WorkflowInterface;

final class NotifierTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<WorkflowInterface> */
    private ObjectProphecy $workflow;

    /** @var ObjectProphecy<ManagerRegistry> */
    private ObjectProphecy $managerRegistry;

    /** @var ObjectProphecy<RestockNotificationEmailManagerInterface> */
    private ObjectProphecy $emailManager;

    /** @var ObjectProphecy<EntityManagerInterface> */
    private ObjectProphecy $objectManager;

    protected function setUp(): void
    {
        $this->workflow = $this->prophesize(WorkflowInterface::class);
        $this->managerRegistry = $this->prophesize(ManagerRegistry::class);
        $this->emailManager = $this->prophesize(RestockNotificationEmailManagerInterface::class);
        $this->objectManager = $this->prophesize(EntityManagerInterface::class);
    }

    /**
     * @test
     */
    public function it_does_nothing_when_product_variant_is_not_in_stock(): void
    {
        $notifier = new Notifier(
            $this->workflow->reveal(),
            $this->managerRegistry->reveal(),
            $this->emailManager->reveal(),
        );

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getOnHand()->willReturn(0);

        $request = $this->prophesize(RestockNotificationRequestInterface::class);
        $request->getProductVariant()->willReturn($productVariant->reveal());

        $notifier->notify($request->reveal());

        // Assert that no methods were called on the workflow
        $this->workflow->can($request->reveal(), RestockNotificationRequestWorkflow::TRANSITION_PROCESS)->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     */
    public function it_does_nothing_when_workflow_cannot_transition_to_process(): void
    {
        $notifier = new Notifier(
            $this->workflow->reveal(),
            $this->managerRegistry->reveal(),
            $this->emailManager->reveal(),
        );

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getOnHand()->willReturn(10);

        $request = $this->prophesize(RestockNotificationRequestInterface::class);
        $request->getProductVariant()->willReturn($productVariant->reveal());

        $this->workflow->can($request->reveal(), RestockNotificationRequestWorkflow::TRANSITION_PROCESS)
            ->willReturn(false);

        $notifier->notify($request->reveal());

        // Assert that apply was not called on the workflow
        $this->workflow->apply($request->reveal(), RestockNotificationRequestWorkflow::TRANSITION_PROCESS)->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     */
    public function it_processes_and_sends_notification_when_conditions_are_met(): void
    {
        // Create the notifier
        $notifier = new Notifier(
            $this->workflow->reveal(),
            $this->managerRegistry->reveal(),
            $this->emailManager->reveal(),
        );

        // Create the product variant
        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getOnHand()->willReturn(10);

        // Create the request
        $request = $this->prophesize(RestockNotificationRequestInterface::class);
        $request->getProductVariant()->willReturn($productVariant->reveal());

        // Mock the getManagerForClass method to return the EntityManagerInterface
        $this->managerRegistry->getManagerForClass($request->reveal()::class)
            ->willReturn($this->objectManager->reveal());

        $this->workflow->can($request->reveal(), RestockNotificationRequestWorkflow::TRANSITION_PROCESS)
            ->willReturn(true);

        // Mock the apply method to return a Marking object
        $marking = new Marking();
        $this->workflow->apply($request->reveal(), RestockNotificationRequestWorkflow::TRANSITION_PROCESS)
            ->willReturn($marking);
        $this->workflow->apply($request->reveal(), RestockNotificationRequestWorkflow::TRANSITION_SEND)
            ->willReturn($marking);

        $notifier->notify($request->reveal());

        // Assert that the workflow transitions were applied
        $this->workflow->apply($request->reveal(), RestockNotificationRequestWorkflow::TRANSITION_PROCESS)->shouldHaveBeenCalled();
        $this->workflow->apply($request->reveal(), RestockNotificationRequestWorkflow::TRANSITION_SEND)->shouldHaveBeenCalled();

        // Assert that the email was sent
        $this->emailManager->sendRestockNotificationEmail($request->reveal())->shouldHaveBeenCalled();

        // Assert that the manager was flushed twice
        $this->objectManager->flush()->shouldHaveBeenCalledTimes(2);
    }
}
