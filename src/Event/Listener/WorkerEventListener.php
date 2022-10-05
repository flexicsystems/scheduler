<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 ThemePoint
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace ThemePoint\Scheduler\Event\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ThemePoint\Scheduler\Constants\WorkerOptions;
use ThemePoint\Scheduler\Event\Event\WorkerRunningEvent;

final class WorkerEventListener implements EventSubscriberInterface
{
    private int $handledEvents;

    public function __construct()
    {
        $this->handledEvents = 0;
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerRunningEvent::class => 'onWorkerRun',
        ];
    }

    public function onWorkerRun(WorkerRunningEvent $event): void
    {
        $event->getWorkerConfiguration()->getIo()?->success(
            \sprintf('[ScheduleWorker] Handle event "%s". Next run: "%s"',
                $event->getScheduleEvent()::class,
                $event->getSchedule()->getExpression()->getNextRunDate()->format('Y-m-d H:i:s')
            )
        );

        ++$this->handledEvents;

        $eventLimit = $event->getWorkerConfiguration()->options[WorkerOptions::SCHEDULE_EVENT_LIMIT];

        if (null !== $eventLimit && $eventLimit > $this->handledEvents) {
            $event->getWorkerConfiguration()->getWorker()->stop();
        }
    }
}
