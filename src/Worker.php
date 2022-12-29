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

final class Worker extends BaseWorker
{
    private bool $shouldStop;

    private readonly WorkerConfiguration $configuration;

    private readonly Timer $timer;

    /**
     * @var array<int, InitializedScheduleEvent>
     */
    private readonly array $initializedScheduleEvent;

    private Timezone $timezone;

    public function __construct(
        WorkerConfiguration $configuration,
        readonly private array $scheduleEvents,
        readonly private EventDispatcherInterface $eventDispatcher,
    ) {
        $configuration->setWorker($this);
        $this->configuration = $configuration;
        $this->shouldStop = false;
        $this->initializedScheduleEvent = InitializedScheduleEventFactory::initialize($this->scheduleEvents);
        $this->timer = new Timer();
        $this->timezone = new Timezone();
        Setup::registerEventListener($this->eventDispatcher);

        $this->eventDispatcher->dispatch(
            new Event\WorkerInitializedEvent(
                $this->configuration,
                $this->initializedScheduleEvent,
            ),
        );
    }

    /**
     * @deprecated Using run() method directly is deprecated and will be removed in v1.1.0. Use start() method instead.
     */
    public function run(): void
    {
        \trigger_deprecation(
            'flexic/scheduler',
            '1.0.2',
            'Using "run"() method directly is deprecated, use "%s" method instead.',
            'run()',
            'start()',
        );

        $this->start();
    }

    public function start(): void
    {
        $this->execute();

        $this->eventDispatcher->dispatch(new Event\WorkerStartEvent($this->configuration));
    }

    public function stop(): void
    {
        $this->shouldStop = true;

        $this->eventDispatcher->dispatch(new Event\WorkerStopEvent($this->configuration));
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
            $scheduleEvents ?? $this->initializedScheduleEvent,
            $this->eventDispatcher,
        );

        $this->eventDispatcher->dispatch(new Event\WorkerUpdateEvent($this->configuration));

        return $worker;
    }

    private function execute(): void
    {
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
}
