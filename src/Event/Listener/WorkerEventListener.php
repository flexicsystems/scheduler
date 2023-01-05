<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 2.0.0
 */

namespace Flexic\Scheduler\Event\Listener;

use Flexic\Scheduler\Constants\WorkerOptions;
use Flexic\Scheduler\Event\Event\Run\WorkerRunEndEvent;
use Flexic\Scheduler\Event\Event\Run\WorkerRunStartEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class WorkerEventListener implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private int $handledEvents;

    public function __construct()
    {
        $this->handledEvents = 0;
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerRunStartEvent::class => 'onWorkerRunStart',
            WorkerRunEndEvent::class => 'onWorkerRunEnd',
        ];
    }

    public function onWorkerRunStart(WorkerRunStartEvent $event): void
    {
        $event->getWorkerConfiguration()->getLogger()->success(
            \sprintf(
                'Handle event "%s". Next run: "%s"',
                $event->getScheduleEvent()::class,
                $event->getSchedule()->getExpression()->getNextRunDate()->format('Y-m-d H:i:s'),
            ),
        );
    }

    public function onWorkerRunEnd(WorkerRunEndEvent $event): void
    {
        ++$this->handledEvents;

        $eventLimit = $event->getWorkerConfiguration()->getOption(WorkerOptions::SCHEDULE_EVENT_LIMIT);

        if (null !== $eventLimit && $eventLimit > $this->handledEvents) {
            $event->getWorkerConfiguration()->getWorker()->stop();
        }
    }
}
