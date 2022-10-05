<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 ThemePoint
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace ThemePoint\Scheduler\Factory;

use ThemePoint\Scheduler\Configuration\InitializedScheduleEvent;
use ThemePoint\Scheduler\Interfaces\ScheduleEventInterface;
use ThemePoint\Scheduler\Schedule;

final class InitializedScheduleEventFactory
{
    public static function initializeList(array $scheduleEvents): array
    {
        return \array_map(static function (ScheduleEventInterface $scheduleEvent) {
            return self::initialize($scheduleEvent);
        }, $scheduleEvents);
    }

    public static function initialize(
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
