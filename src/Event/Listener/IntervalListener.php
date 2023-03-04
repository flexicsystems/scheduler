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
use Flexic\Scheduler\Event\Event\Interval\WorkerIntervalEndEvent;
use Flexic\Scheduler\Event\Event\Interval\WorkerIntervalStartEvent;
use Flexic\Scheduler\Exception\InvalidArgumentException;
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

        if (null !== $memoryLimit && !\is_int($memoryLimit) && !\is_float($memoryLimit) && !\is_string($memoryLimit)) {
            throw new InvalidArgumentException(\sprintf('Option "%s" must be of type "int" or "float".', WorkerOptions::MEMORY_LIMIT));
        }

        if (\is_string($memoryLimit)) {
            $memoryLimit = (float) $memoryLimit;
        }

        if (null !== $memoryLimit && \memory_get_usage() > $memoryLimit) {
            $event->getWorkerConfiguration()->getLogger()->info(\sprintf('Reached memory limit of %s.', (string) $memoryLimit));
            $event->getWorkerConfiguration()->getWorker()->stop();
        }

        $timeLimit = $event->getWorkerConfiguration()->getOption(WorkerOptions::TIME_LIMIT);

        if (null !== $timeLimit && !\is_int($timeLimit) && !\is_float($timeLimit) && !\is_string($timeLimit)) {
            throw new InvalidArgumentException(\sprintf('Option "%s" must be of type "int" or "float".', WorkerOptions::TIME_LIMIT));
        }

        if (\is_string($timeLimit)) {
            $timeLimit = (float) $timeLimit;
        }

        if (null !== $timeLimit && \time() - $this->startTime > $timeLimit) {
            $event->getWorkerConfiguration()->getLogger()->info(\sprintf('Reached time limit of %s.', (string) $timeLimit));
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
