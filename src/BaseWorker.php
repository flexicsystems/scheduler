<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler;

use Flexic\Scheduler\Event\Listener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class BaseWorker
{
    protected function setupEventSystem(EventDispatcherInterface $eventDispatcher): void
    {
        $eventDispatcher->addSubscriber(new Listener\WorkerEventListener());
        $eventDispatcher->addSubscriber(new Listener\IntervalListener());
        $eventDispatcher->addSubscriber(new Listener\WorkerLifecycleListener());
        $eventDispatcher->addSubscriber(new Listener\WorkerExecutionListener());
    }
}
