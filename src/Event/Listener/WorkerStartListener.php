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
use ThemePoint\Scheduler\Event\Event\WorkerEventInterface;
use ThemePoint\Scheduler\Event\Event\WorkerStartEvent;

final class WorkerStartListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            WorkerStartEvent::class => 'onWorkerStart',
        ];
    }

    public function onWorkerStart(WorkerEventInterface $event): void
    {
        echo 'Start Worker' . \PHP_EOL;
    }
}
