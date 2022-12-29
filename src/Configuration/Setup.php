<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Configuration;

use Flexic\Scheduler\Event\Listener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Setup
{
    public static function registerEventListener(
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $eventDispatcher->addSubscriber(new Listener\WorkerEventListener());
        $eventDispatcher->addSubscriber(new Listener\IntervalListener());
        $eventDispatcher->addSubscriber(new Listener\WorkerLifecycleListener());
    }
}
