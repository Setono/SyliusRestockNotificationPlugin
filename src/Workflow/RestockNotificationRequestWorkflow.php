<?php

declare(strict_types=1);

namespace Setono\SyliusRestockNotificationPlugin\Workflow;

use Setono\SyliusRestockNotificationPlugin\Model\RestockNotificationRequestInterface;
use Symfony\Component\Workflow\Transition;

final class RestockNotificationRequestWorkflow
{
    private const PROPERTY_NAME = 'state';

    final public const NAME = 'setono_sylius_restock_notification__restock_notification_request';

    public const TRANSITION_PROCESS = 'process';

    public const TRANSITION_SEND = 'send';

    public const TRANSITION_FAIL = 'fail';

    public const TRANSITION_RESEND = 'resend';

    public const TRANSITION_RETRY = 'retry';

    private function __construct()
    {
    }

    /**
     * @return non-empty-list<string>
     */
    public static function getStates(): array
    {
        return [
            RestockNotificationRequestInterface::STATE_PENDING,
            RestockNotificationRequestInterface::STATE_PROCESSING,
            RestockNotificationRequestInterface::STATE_SENT,
            RestockNotificationRequestInterface::STATE_FAILED,
        ];
    }

    public static function getConfig(): array
    {
        $transitions = [];
        foreach (self::getTransitions() as $transition) {
            $transitions[$transition->getName()] = [
                'from' => $transition->getFroms(),
                'to' => $transition->getTos(),
            ];
        }

        return [
            self::NAME => [
                'type' => 'state_machine',
                'marking_store' => [
                    'type' => 'method',
                    'property' => self::PROPERTY_NAME,
                ],
                'supports' => RestockNotificationRequestInterface::class,
                'initial_marking' => RestockNotificationRequestInterface::STATE_PENDING,
                'places' => self::getStates(),
                'transitions' => $transitions,
            ],
        ];
    }

    /**
     * @return non-empty-list<Transition>
     */
    public static function getTransitions(): array
    {
        return [
            new Transition(
                self::TRANSITION_PROCESS,
                RestockNotificationRequestInterface::STATE_PENDING,
                RestockNotificationRequestInterface::STATE_PROCESSING,
            ),
            new Transition(
                self::TRANSITION_SEND,
                RestockNotificationRequestInterface::STATE_PROCESSING,
                RestockNotificationRequestInterface::STATE_SENT,
            ),
            new Transition(
                self::TRANSITION_FAIL,
                [RestockNotificationRequestInterface::STATE_PENDING, RestockNotificationRequestInterface::STATE_PROCESSING],
                RestockNotificationRequestInterface::STATE_FAILED,
            ),
            new Transition(
                self::TRANSITION_RESEND,
                RestockNotificationRequestInterface::STATE_SENT,
                RestockNotificationRequestInterface::STATE_PENDING,
            ),
            new Transition(
                self::TRANSITION_RETRY,
                RestockNotificationRequestInterface::STATE_FAILED,
                RestockNotificationRequestInterface::STATE_PENDING,
            ),
        ];
    }
}
