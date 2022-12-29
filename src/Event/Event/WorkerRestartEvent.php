<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Event\Event;

use Flexic\Scheduler\Configuration\WorkerConfiguration;

final class WorkerRestartEvent implements WorkerEventInterface
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
