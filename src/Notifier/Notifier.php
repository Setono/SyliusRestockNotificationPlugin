<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Notifier;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusRestockNotificationPlugin\EmailManager\RestockNotificationEmailManagerInterface;
use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Setono\SyliusRestockNotificationPlugin\Workflow\NotificationWorkflow;
use Symfony\Component\Workflow\Registry;

final class Notifier implements NotifierInterface
{
    use ORMTrait;

    public function __construct(
        // todo inject workflow directly
        private readonly Registry $workflowRegistry,
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

        $stateMachine = $this->workflowRegistry->get($restockNotificationRequest, NotificationWorkflow::NAME);
        if (!$stateMachine->can($restockNotificationRequest, NotificationWorkflow::TRANSITION_PROCESS)) {
            return; // todo throw exception instead?
        }

        $stateMachine->apply($restockNotificationRequest, NotificationWorkflow::TRANSITION_PROCESS);

        $manager = $this->getManager($restockNotificationRequest);
        $manager->flush();

        $this->restockNotificationEmailManager->sendRestockNotificationEmail($restockNotificationRequest);

        $stateMachine->apply($restockNotificationRequest, NotificationWorkflow::TRANSITION_SEND);
        $manager->flush();
    }
}
