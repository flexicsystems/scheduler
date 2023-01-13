<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 2.0.0
 */

namespace Flexic\Scheduler\Event\Listener;

use Flexic\Scheduler\Constants\WorkerOptions;
use Flexic\Scheduler\Event\Event;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class WorkerExecutionListener implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private array $parallelExecution;

    public function __construct()
    {
        $this->parallelExecution = [];
    }

    public static function getSubscribedEvents()
    {
        return [
            Event\Execute\WorkerExecuteEvent::class => 'onWorkerExecute',
            Event\Execute\WorkerExecuteSequentialEvent::class => 'onWorkerExecuteSequential',
            Event\Execute\WorkerExecuteParallelInitEvent::class => 'onInitParallelExecution',
            Event\Execute\WorkerExecuteParallelResumeEvent::class => 'onResumeParallelExecution',
        ];
    }

    public function onWorkerExecute(Event\Execute\WorkerExecuteEvent $event): void
    {
        $eventDispatcher = $event->getEventDispatcher();

        $scheduleEvent = $event->getScheduleEvent();

        $eventDispatcher->dispatch(new Event\Run\WorkerRunStartEvent(
            $event->getWorkerConfiguration(),
            $scheduleEvent,
            $event->getSchedule(),
            $event->getInterval(),
        ));

        if ($event->getWorkerConfiguration()->getOption(WorkerOptions::PARALLEL_EXECUTION)) {
            $eventDispatcher->dispatch(new Event\Execute\WorkerExecuteParallelInitEvent($scheduleEvent));
        } else {
            $eventDispatcher->dispatch(new Event\Execute\WorkerExecuteSequentialEvent($scheduleEvent));
        }

        $eventDispatcher->dispatch(new Event\Run\WorkerRunEndEvent(
            $event->getWorkerConfiguration(),
            $scheduleEvent,
            $event->getSchedule(),
            $event->getInterval(),
        ));
    }

    public function onWorkerExecuteSequential(Event\Execute\WorkerExecuteSequentialEvent $event): void
    {
        $scheduleEvent = $event->getScheduleEvent();

        $scheduleEvent();
    }

    public function onInitParallelExecution(Event\Execute\WorkerExecuteParallelInitEvent $event): void
    {
        $scheduleEvent = $event->getScheduleEvent();

        $this->parallelExecution[] = new \Fiber(static function () use ($scheduleEvent): void {
            \Fiber::suspend();

            $scheduleEvent();
        });
    }

    public function onResumeParallelExecution(Event\Execute\WorkerExecuteParallelResumeEvent $event): void
    {
        $maxParallelExecution = $event->getWorkerConfiguration()->getOption(WorkerOptions::PARALLEL_EXECUTION_LIMIT);

        if (0 >= $maxParallelExecution) {
            $maxParallelExecution = \count($this->parallelExecution);
        }

        while (\count($this->parallelExecution) > 0) {
            $started = [];

            foreach ($this->parallelExecution as $key => $fiber) {
                if (\count($started) >= $maxParallelExecution) {
                    break;
                }

                if (!$fiber->isStarted()) {
                    $fiber->start();
                    $started[$key] = $fiber;
                }
            }

            foreach ($started as $key => $fiber) {
                $fiber->resume();
                unset($this->parallelExecution[$key]);
            }
        }
    }
}
