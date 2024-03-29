<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 2.0.0
 */

namespace Flexic\Scheduler\Configuration;

use Flexic\Scheduler\Interfaces\AbstractScheduleEventInterface;
use Flexic\Scheduler\Interfaces\ScheduleInterface;

final class InitializedScheduleEvent
{
    public function __construct(
        readonly private AbstractScheduleEventInterface $scheduleEvent,
        readonly private ScheduleInterface $schedule,
    ) {
    }

    public function getScheduleEvent(): AbstractScheduleEventInterface
    {
        return $this->scheduleEvent;
    }

    public function getSchedule(): ScheduleInterface
    {
        return $this->schedule;
    }
}
