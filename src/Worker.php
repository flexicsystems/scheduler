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
use Flexic\Scheduler\Event\Event;
use Flexic\Scheduler\Factory\InitializedScheduleEventFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Worker extends BaseWorker
{
    /**
     * @var array<int, InitializedScheduleEvent>
     */
    private readonly array $initializedScheduleEvent;

    public function __construct(
        readonly private WorkerConfiguration $configuration,
        readonly private array $scheduleEvents,
        readonly private EventDispatcherInterface $eventDispatcher,
        private bool $initialized = false,
        private bool $shouldStop = false,
        readonly private Time\Timer $timer = new Time\Timer(),
        readonly private Time\Timezone $timezone = new Time\Timezone(),
    ) {
        $this->configuration->setWorker($this);
        $this->initializedScheduleEvent = InitializedScheduleEventFactory::initialize(
            $this->scheduleEvents,
            $this,
        );

        Setup::registerEventListener($this->eventDispatcher);

        $this->eventDispatcher->dispatch(
            new Event\Lifecycle\WorkerInitializedEvent(
                $this->configuration,
                $this->initializedScheduleEvent,
            ),
        );
    }

    public function start(): void
    {
        if ($this->initialized) {
            $this->timer->waitForNextTick();
        }

        $this->initialized = true;

        $this->shouldStop = false;

        $this->execute();

        $this->eventDispatcher->dispatch(new Event\Lifecycle\WorkerStartEvent($this->configuration));
    }

    public function stop(): void
    {
        $this->shouldStop = true;

        $this->eventDispatcher->dispatch(new Event\Lifecycle\WorkerStopEvent($this->configuration));
    }

    public function restart(): void
    {
        $this->shouldStop = true;

        $worker = new $this(
            $this->configuration,
            $this->scheduleEvents,
            $this->eventDispatcher,
        );

        (new \ReflectionClass($this))->getMethod('execute')->invoke($worker);
    }

    public function update(
        ?WorkerConfiguration $configuration,
        ?array $scheduleEvents,
    ): self {
        $this->stop();

        $worker = new $this(
            $configuration ?? $this->configuration,
            $scheduleEvents ?? $this->scheduleEvents,
            $this->eventDispatcher,
        );

        $this->eventDispatcher->dispatch(new Event\Lifecycle\WorkerUpdateEvent($this->configuration));

        $worker->start();

        return $worker;
    }

    private function execute(): void
    {
        $interval = 1;

        while (!$this->shouldStop) {
            $this->eventDispatcher->dispatch(new Event\Interval\WorkerIntervalStartEvent($this->configuration, $interval));

            foreach ($this->initializedScheduleEvent as $event) {
                /** @var Schedule $schedule */
                $schedule = $event->getSchedule();

                $this->timezone->set($schedule->getTimezone());

                if ($schedule->getExpression()->isDue()) {
                    $scheduleEvent = $event->getScheduleEvent();

                    $this->eventDispatcher->dispatch(new Event\Execute\WorkerExecuteEvent(
                        $this->configuration,
                        $this->eventDispatcher,
                        $scheduleEvent,
                        $schedule,
                        $interval,
                    ));
                }

                $this->timezone->default();
            }

            $this->eventDispatcher->dispatch(new Event\Execute\WorkerExecuteParallelResumeEvent());

            $this->eventDispatcher->dispatch(new Event\Interval\WorkerIntervalEndEvent($this->configuration, $interval));

            ++$interval;

            if ($this->shouldStop) { // @phpstan-ignore-line Check before sleep to prevent longer run than required.
                return;
            }

            $this->timer->waitForNextTick();
        }
    }
}
