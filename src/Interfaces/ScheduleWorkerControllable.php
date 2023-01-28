<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 2.0.0
 */

namespace Flexic\Scheduler\Interfaces;

use Flexic\Scheduler\Worker;

interface ScheduleWorkerControllable
{
    public function setWorker(Worker $worker): void;
}
