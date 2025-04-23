<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Notifier;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusRestockNotificationPlugin\EmailManager\RestockNotificationEmailManagerInterface;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Setono\SyliusRestockNotificationPlugin\Workflow\RestockNotificationRequestWorkflow;
use Symfony\Component\Workflow\WorkflowInterface;

final class Notifier implements NotifierInterface
{
    use ORMTrait;

    public function __construct(
        private readonly WorkflowInterface $restockNotificationRequestWorkflow,
        ManagerRegistry $managerRegistry,
        private readonly RestockNotificationEmailManagerInterface $restockNotificationEmailManager,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function notify(RestockNotificationRequestInterface $restockNotificationRequest): void
    {
        $onHand = (int) $restockNotificationRequest->getProductVariant()?->getOnHand();
        if ($onHand <= 0) {
            return;
        }

        if (!$this->restockNotificationRequestWorkflow->can($restockNotificationRequest, RestockNotificationRequestWorkflow::TRANSITION_PROCESS)) {
            return; // todo throw exception instead?
        }

        $this->restockNotificationRequestWorkflow->apply($restockNotificationRequest, RestockNotificationRequestWorkflow::TRANSITION_PROCESS);

        $manager = $this->getManager($restockNotificationRequest);
        $manager->flush();

        $this->restockNotificationEmailManager->sendRestockNotificationEmail($restockNotificationRequest);

        $this->restockNotificationRequestWorkflow->apply($restockNotificationRequest, RestockNotificationRequestWorkflow::TRANSITION_SEND);
        $manager->flush();
    }
}
