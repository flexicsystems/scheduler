<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 ThemePoint
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace ThemePoint\Scheduler;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use ThemePoint\Scheduler\Event\Listener;

final class Setup
{
    public static function registerEventListener(
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $eventDispatcher->addSubscriber(new Listener\WorkerStartListener());
        $eventDispatcher->addSubscriber(new Listener\IntervalListener());
    }
}
