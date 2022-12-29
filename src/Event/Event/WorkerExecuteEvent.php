<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Event\Event;

use Flexic\Scheduler\Configuration\WorkerConfiguration;
use Flexic\Scheduler\Interfaces\ScheduleEventInterface;
use Flexic\Scheduler\Interfaces\ScheduleInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class WorkerExecuteEvent implements WorkerEventInterface
{
    public function __construct(
        readonly private WorkerConfiguration $workerConfiguration,
        readonly private EventDispatcherInterface $eventDispatcher,
        readonly private ScheduleEventInterface $scheduleEvent,
        readonly private ScheduleInterface $schedule,
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
