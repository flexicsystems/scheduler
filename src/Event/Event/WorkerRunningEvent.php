<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 ThemePoint
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace ThemePoint\Scheduler\Event\Event;

use ThemePoint\Scheduler\Configuration\WorkerConfiguration;
use ThemePoint\Scheduler\Interfaces\ScheduleEventInterface;
use ThemePoint\Scheduler\Interfaces\ScheduleInterface;

final class WorkerRunningEvent implements WorkerEventInterface
{
    public function __construct(
        readonly private WorkerConfiguration $workerConfiguration,
        readonly private ScheduleEventInterface $scheduleEvent,
        readonly private ScheduleInterface $schedule,
        readonly private int $interval,
    ) {
    }

    public function getWorkerConfiguration(): WorkerConfiguration
    {
        return $this->workerConfiguration;
    }

    public function getScheduleEvent(): ScheduleEventInterface
    {
        return $this->scheduleEvent;
    }

    public function getSchedule(): ScheduleInterface
    {
        return $this->schedule;
    }

    public function getInterval(): int
    {
        return $this->interval;
    }
}
