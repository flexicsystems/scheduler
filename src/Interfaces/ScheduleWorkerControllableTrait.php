<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Interfaces;

use Flexic\Scheduler\Worker;

trait ScheduleWorkerControllableTrait
{
    protected null|Worker $worker = null;

    public function setWorker(Worker $worker): void
    {
        $this->worker = $worker;
    }
}
