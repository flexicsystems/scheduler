<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 2.0.0
 */

namespace Flexic\Scheduler\Event\Event\Lifecycle;

use Flexic\Scheduler\Configuration\WorkerConfiguration;
use Flexic\Scheduler\Event\Event\WorkerEventInterface;

final class WorkerUpdateEvent implements WorkerEventInterface
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
