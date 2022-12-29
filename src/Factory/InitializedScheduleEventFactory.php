<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Factory;

use Flexic\Scheduler\Configuration\InitializedScheduleEvent;
use Flexic\Scheduler\Interfaces\ScheduleEventInterface;
use Flexic\Scheduler\Interfaces\ScheduleWorkerControllable;
use Flexic\Scheduler\Schedule;
use Flexic\Scheduler\Worker;

final class InitializedScheduleEventFactory
{
    public static function initialize(
        array $scheduleEvents,
        Worker $worker,
    ): array {
        return \array_map(static function (ScheduleEventInterface $scheduleEvent) use ($worker): InitializedScheduleEvent {
            if ($scheduleEvent instanceof ScheduleWorkerControllable) {
                $scheduleEvent->setWorker($worker);
            }

            return self::initializeEvent($scheduleEvent);
        }, $scheduleEvents);
    }

    public static function initializeEvent(
        ScheduleEventInterface $scheduleEvent,
    ): InitializedScheduleEvent {
        $schedule = new Schedule();

        $scheduleEvent->configure($schedule);

        return new InitializedScheduleEvent(
            $scheduleEvent,
            $schedule,
        );
    }
}
