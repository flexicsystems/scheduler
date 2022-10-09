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

use Flexic\Scheduler\Event\Event\WorkerStartEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class WorkerStartListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            WorkerStartEvent::class => 'onWorkerStart',
        ];
    }

    public function onWorkerStart(WorkerStartEvent $event): void
    {
        $event->getWorkerConfiguration()->getIo()?->success('[ScheduleWorker] Starting worker');
    }
}
