<?php

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