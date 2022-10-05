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
use ThemePoint\Scheduler\Configuration\InitializedScheduleEvent;
use ThemePoint\Scheduler\Configuration\WorkerConfiguration;
use ThemePoint\Scheduler\Event\Event;
use ThemePoint\Scheduler\Factory\InitializedScheduleEventFactory;

final class Worker
{
    private bool $shouldStop = false;

    private readonly WorkerConfiguration $configuration;

    /**
     * @var array<int, InitializedScheduleEvent>
     */
    private readonly array $initializedScheduleEvent;

    public function __construct(
        WorkerConfiguration $configuration,
        array $scheduleEvents,
        readonly private EventDispatcherInterface $eventDispatcher,
    ) {
        $configuration->setWorker($this);
        $this->configuration = $configuration;
        $this->shouldStop = false;
        $this->initializedScheduleEvent = InitializedScheduleEventFactory::initializeList($scheduleEvents);
        Setup::registerEventListener($this->eventDispatcher);
    }

    public function run(): void
    {
        $this->eventDispatcher->dispatch(new Event\WorkerStartEvent($this->configuration));

        $interval = 1;

        while (!$this->shouldStop) {
            $this->eventDispatcher->dispatch(new Event\WorkerIntervalStartEvent($this->configuration, $interval));

            foreach ($this->initializedScheduleEvent as $event) {
                /** @var Schedule $schedule */
                $schedule = $event->getSchedule();

                if ($schedule->getExpression()->isDue()) {
                    $scheduleEvent = $event->getScheduleEvent();

                    $scheduleEvent();

                    $this->eventDispatcher->dispatch(new Event\WorkerRunningEvent($this->configuration, $scheduleEvent, $schedule, $interval));
                }
            }

            $this->eventDispatcher->dispatch(new Event\WorkerIntervalEndEvent($this->configuration, $interval));

            ++$interval;
            \sleep(60);
        }
    }

    public function stop(): void
    {
        $this->shouldStop = true;
    }
}
