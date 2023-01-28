<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 2.0.0
 */

namespace Flexic\Scheduler\Event\Event\Execute;

use Flexic\Scheduler\Configuration\WorkerConfiguration;
use Flexic\Scheduler\Event\Event\WorkerEventInterface;
use Flexic\Scheduler\Interfaces\AbstractScheduleEventInterface;
use Flexic\Scheduler\Schedule;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class WorkerExecuteEvent implements WorkerEventInterface
{
    public function __construct(
        readonly private WorkerConfiguration $workerConfiguration,
        readonly private EventDispatcherInterface $eventDispatcher,
        readonly private AbstractScheduleEventInterface $scheduleEvent,
        readonly private Schedule $schedule,
        readonly private int $interval,
    ) {
    }

    public function getWorkerConfiguration(): WorkerConfiguration
    {
        return $this->workerConfiguration;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function getScheduleEvent(): AbstractScheduleEventInterface
    {
        return $this->scheduleEvent;
    }

    public function getSchedule(): Schedule
    {
        return $this->schedule;
    }

    public function getInterval(): int
    {
        return $this->interval;
    }
}
