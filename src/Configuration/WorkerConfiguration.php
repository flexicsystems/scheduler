<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 ThemePoint
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace ThemePoint\Scheduler\Configuration;

use ThemePoint\Scheduler\Constants\WorkerOptions;
use ThemePoint\Scheduler\Worker;

final class WorkerConfiguration extends Configuration
{
    public readonly array $options;

    public Worker $worker;

    public function __construct(
        array $options = [],
    ) {
        $this->options = $this->resolve($options, [
            WorkerOptions::SCHEDULE_EVENT_LIMIT => null,
            WorkerOptions::TIME_LIMIT => null,
            WorkerOptions::MEMORY_LIMIT => null,
            WorkerOptions::INTERVAL_LIMIT => null,
        ]);
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
