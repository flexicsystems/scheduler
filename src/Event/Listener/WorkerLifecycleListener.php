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

use Flexic\Scheduler\Event\Event\WorkerInitializedEvent;
use Flexic\Scheduler\Event\Event\WorkerRestartEvent;
use Flexic\Scheduler\Event\Event\WorkerStartEvent;
use Flexic\Scheduler\Event\Event\WorkerStopEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class WorkerLifecycleListener implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public static function getSubscribedEvents()
    {
        return [
            WorkerInitializedEvent::class => 'onWorkerInitialized',
            WorkerStartEvent::class => 'onWorkerStart',
            WorkerStopEvent::class => 'onWorkerStop',
            WorkerRestartEvent::class => 'onWorkerRestart',
        ];
    }

    public function onWorkerRun(WorkerInitializedEvent $event): void
    {
        $event->getWorkerConfiguration()->getLogger()->success(
            \sprintf('Initialized worker with %s schedule events.', \count($event->getScheduleEvents()))
        );
    }

    public function onWorkerStart(WorkerStartEvent $event): void
    {
        $event->getWorkerConfiguration()->getLogger()?->success('Starting worker');
    }

    public function onWorkerStop(WorkerStartEvent $event): void
    {
        $event->getWorkerConfiguration()->getLogger()?->success('Stopping worker');
    }

    public function onWorkerRestart(WorkerStartEvent $event): void
    {
        $event->getWorkerConfiguration()->getLogger()?->success('Restarting worker');
    }
}
