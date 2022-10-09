<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 ThemePoint
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Configuration;

use Symfony\Component\Console\Style\SymfonyStyle;
use Flexic\Scheduler\Constants\WorkerOptions;
use Flexic\Scheduler\Worker;

final class WorkerConfiguration extends Configuration
{
    public readonly array $options;

    public Worker $worker;

    public function __construct(
        array $options = [],
        readonly private ?SymfonyStyle $io = null,
    ) {
        $this->options = $this->resolve($options, [
            WorkerOptions::SCHEDULE_EVENT_LIMIT => null,
            WorkerOptions::TIME_LIMIT => null,
            WorkerOptions::MEMORY_LIMIT => null,
            WorkerOptions::INTERVAL_LIMIT => null,
        ]);
    }

    public function getIo(): ?SymfonyStyle
    {
        return $this->io;
    }

    public function setWorker(Worker $worker): void
    {
        $this->worker = $worker;
    }

    public function getWorker(): Worker
    {
        return $this->worker;
    }
}
