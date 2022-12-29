<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Event\Listener;

use Flexic\Scheduler\Constants\WorkerOptions;
use Flexic\Scheduler\Event\Event\WorkerIntervalEndEvent;
use Flexic\Scheduler\Event\Event\WorkerIntervalStartEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class IntervalListener implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private int $startTime;

    public function __construct()
    {
        $this->startTime = \time();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerIntervalStartEvent::class => 'onWorkerIntervalStart',
            WorkerIntervalEndEvent::class => 'onWorkerIntervalEnd',
        ];
    }

    public function onWorkerIntervalStart(WorkerIntervalStartEvent $event): void
    {
        $event->getWorkerConfiguration()->getLogger()->info(\sprintf('Interval %s started', $event->getInterval()));

        $memoryLimit = $event->getWorkerConfiguration()->getOption(WorkerOptions::MEMORY_LIMIT);

        if (null !== $memoryLimit && \memory_get_usage() > $memoryLimit) {
            $event->getWorkerConfiguration()->getWorker()->stop();
        }

        $timeLimit = $event->getWorkerConfiguration()->getOption(WorkerOptions::TIME_LIMIT);

        if (null !== $timeLimit && \time() - $this->startTime > $timeLimit) {
            $event->getWorkerConfiguration()->getWorker()->stop();
        }
    }

    public function onWorkerIntervalEnd(WorkerIntervalEndEvent $event): void
    {
        if ($event->getWorkerConfiguration()->getOption(WorkerOptions::INTERVAL_LIMIT) !== null && $event->getWorkerConfiguration()->getOption(WorkerOptions::INTERVAL_LIMIT) <= $event->getInterval()) {
            $event->getWorkerConfiguration()->getWorker()->stop();
        }
    }
}
