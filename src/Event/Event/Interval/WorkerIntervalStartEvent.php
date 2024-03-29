<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 2.0.0
 */

namespace Flexic\Scheduler\Event\Event\Interval;

use Flexic\Scheduler\Configuration\WorkerConfiguration;
use Flexic\Scheduler\Event\Event\WorkerEventInterface;

final class WorkerIntervalStartEvent implements WorkerEventInterface
{
    public function __construct(
        readonly private WorkerConfiguration $workerConfiguration,
        readonly private int $interval,
    ) {
    }

    public function getWorkerConfiguration(): WorkerConfiguration
    {
        return $this->workerConfiguration;
    }

    public function getInterval(): int
    {
        return $this->interval;
    }
}
