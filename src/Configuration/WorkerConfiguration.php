<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Configuration;

use Flexic\Scheduler\Constants\WorkerOptions;
use Flexic\Scheduler\Worker;
use Flexic\Scheduler\WorkerLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class WorkerConfiguration extends Configuration
{
    public Worker $worker;

    public WorkerLogger $logger;

    private readonly array $options;

    public function __construct(
        array $options = [],
        ?SymfonyStyle $io = null,
        ?LoggerInterface $logger = null,
    ) {
        $this->options = $this->resolve($options, [
            WorkerOptions::SCHEDULE_EVENT_LIMIT => null,
            WorkerOptions::TIME_LIMIT => null,
            WorkerOptions::MEMORY_LIMIT => null,
            WorkerOptions::INTERVAL_LIMIT => null,
            WorkerOptions::PARALLEL_EXECUTION => false,
            WorkerOptions::ASYNCHRONOUS_EXECUTION => false,
            WorkerOptions::ASYNCHRONOUS_EXECUTION_EXECUTABLE => null,
        ]);

        if ($this->getOption(WorkerOptions::ASYNCHRONOUS_EXECUTION) && $this->getOption(WorkerOptions::PARALLEL_EXECUTION)) {
            throw new \RuntimeException('You can not use parallel execution and asynchronous execution at the same time.');
        }

        if ($this->getOption(WorkerOptions::ASYNCHRONOUS_EXECUTION) && (!\is_file((string) $this->getOption(WorkerOptions::ASYNCHRONOUS_EXECUTION_EXECUTABLE)) || !\is_executable((string) $this->getOption(WorkerOptions::ASYNCHRONOUS_EXECUTION_EXECUTABLE)))) {
            throw new \RuntimeException('The asynchronous execution executable is not a file or not executable.');
        }

        $this->logger = new WorkerLogger(
            $io,
            $logger,
        );
    }

    public function getLogger(): WorkerLogger
    {
        return $this->logger;
    }

    public function setWorker(Worker $worker): void
    {
        $this->worker = $worker;
    }

    public function getWorker(): Worker
    {
        return $this->worker;
    }

    public function getOption(string $option): mixed
    {
        if (!\array_key_exists($option, $this->options)) {
            throw new \InvalidArgumentException(\sprintf('The option "%s" does not exist.', $option));
        }

        return $this->options[$option];
    }
}
