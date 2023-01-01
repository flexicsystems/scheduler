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
use Flexic\Scheduler\Interfaces\ScheduleEventFactoryInterface;
use Flexic\Scheduler\Interfaces\ScheduleEventInterface;
use Flexic\Scheduler\Interfaces\ScheduleWorkerControllable;
use Flexic\Scheduler\Resolver\ScheduleEventInputResolver;
use Flexic\Scheduler\Schedule;
use Flexic\Scheduler\Worker;

final class InitializedScheduleEventFactory
{
    public function __construct(
        readonly private Worker $worker,
        readonly private ScheduleEventInputResolver $scheduleEventInputResolver = new ScheduleEventInputResolver(),
    ) {}

    public function initialize(
        array $scheduleEvents,
    ): array {
        return \array_map(
            function (ScheduleEventInterface $scheduleEvent): InitializedScheduleEvent {
                return $this->initializeEvent($scheduleEvent);
            },
            $this->scheduleEventInputResolver->resolve($scheduleEvents),
        );
    }

    public function initializeEvent(
        ScheduleEventInterface $scheduleEvent,
    ): InitializedScheduleEvent {
        $schedule = new Schedule();

        if ($scheduleEvent instanceof ScheduleWorkerControllable) {
            $scheduleEvent->setWorker($this->worker);
        }

        $scheduleEvent->configure($schedule);

        return new InitializedScheduleEvent(
            $scheduleEvent,
            $schedule,
        );
    }
}
