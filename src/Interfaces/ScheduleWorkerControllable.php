<?php

namespace Flexic\Scheduler\Interfaces;

use Flexic\Scheduler\Worker;
interface ScheduleWorkerControllable
{
    public function setWorker(Worker $worker): void;
}