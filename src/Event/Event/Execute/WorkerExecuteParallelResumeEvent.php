<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 2.0.0
 */

namespace Flexic\Scheduler\Event\Event\Execute;

use Flexic\Scheduler\Configuration\WorkerConfiguration;

final class WorkerExecuteParallelResumeEvent
{
    public function __construct(
        readonly private WorkerConfiguration $workerConfiguration,
    ) {
    }

    public function getWorkerConfiguration(): WorkerConfiguration
    {
        return $this->workerConfiguration;
    }
}
