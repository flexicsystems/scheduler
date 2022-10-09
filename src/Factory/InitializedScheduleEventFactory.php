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
use Flexic\Scheduler\Schedule;

final class InitializedScheduleEventFactory
{
    public static function initialize(array $scheduleEvents): array
    {
        return \array_map(static function (ScheduleEventInterface $scheduleEvent) {
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
