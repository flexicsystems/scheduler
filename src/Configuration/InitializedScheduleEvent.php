<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 ThemePoint
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace ThemePoint\Scheduler\Configuration;

use ThemePoint\Scheduler\Interfaces\ScheduleEventInterface;
use ThemePoint\Scheduler\Interfaces\ScheduleInterface;

final class InitializedScheduleEvent
{
    public function __construct(
        readonly private ScheduleEventInterface $scheduleEvent,
        readonly private ScheduleInterface $schedule,
    ) {
    }

    public function getScheduleEvent(): ScheduleEventInterface
    {
        return $this->scheduleEvent;
    }

    public function getSchedule(): ScheduleInterface
    {
        return $this->schedule;
    }
}
