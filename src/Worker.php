<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler;

use Flexic\Scheduler\Configuration\InitializedScheduleEvent;
use Flexic\Scheduler\Configuration\Setup;
use Flexic\Scheduler\Configuration\WorkerConfiguration;
use Flexic\Scheduler\DateTime\Timer;
use Flexic\Scheduler\DateTime\Timezone;
use Flexic\Scheduler\Event\Event;
use Flexic\Scheduler\Factory\InitializedScheduleEventFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Worker
{
    private bool $shouldStop = false;

    private readonly WorkerConfiguration $configuration;

    private readonly Timer $timer;

    /**
     * @var array<int, InitializedScheduleEvent>
     */
    private readonly array $initializedScheduleEvent;

    private Timezone $timezone;

    public function __construct(
        WorkerConfiguration $configuration,
        array $scheduleEvents,
        readonly private EventDispatcherInterface $eventDispatcher,
    ) {
        $configuration->setWorker($this);
        $this->configuration = $configuration;
        $this->shouldStop = false;
        $this->initializedScheduleEvent = InitializedScheduleEventFactory::initializeList($scheduleEvents);
        $this->timer = new Timer();

        $configuration->getIo()?->success(\sprintf('Initialized worker with %s schedule events.', \count($this->initializedScheduleEvent)));
        Setup::registerEventListener($this->eventDispatcher);
        $this->timezone = new Timezone();
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

                $this->timezone->set($schedule->getTimezone());

                if ($schedule->getExpression()->isDue()) {
                    $scheduleEvent = $event->getScheduleEvent();

                    $scheduleEvent();

                    $this->eventDispatcher->dispatch(new Event\WorkerRunningEvent($this->configuration, $scheduleEvent, $schedule, $interval));
                }

                $this->timezone->default();
            }

            $this->eventDispatcher->dispatch(new Event\WorkerIntervalEndEvent($this->configuration, $interval));

            ++$interval;
            $this->timer->waitForNextTick();
        }
    }

    public function stop(): void
    {
        $this->shouldStop = true;
    }
}
