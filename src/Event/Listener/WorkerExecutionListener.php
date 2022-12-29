<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Event\Listener;

use Flexic\Scheduler\Event\Event;
use Flexic\Scheduler\Event\Event\WorkerExecuteEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class WorkerExecutionListener implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public static function getSubscribedEvents()
    {
        return [
            WorkerExecuteEvent::class => 'onWorkerExecute',
        ];
    }

    public function onWorkerExecute(WorkerExecuteEvent $event): void
    {
        $eventDispatcher = $event->getEventDispatcher();

        $scheduleEvent = $event->getScheduleEvent();

        $eventDispatcher->dispatch(new Event\WorkerRunStartEvent(
            $event->getWorkerConfiguration(),
            $scheduleEvent,
            $event->getSchedule(),
            $event->getInterval(),
        ));

        $scheduleEvent(); // ToDo

        $eventDispatcher->dispatch(new Event\WorkerRunEndEvent(
            $event->getWorkerConfiguration(),
            $scheduleEvent,
            $event->getSchedule(),
            $event->getInterval(),
        ));
    }
}
