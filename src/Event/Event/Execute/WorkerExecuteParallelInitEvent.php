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

use Flexic\Scheduler\Interfaces\ScheduleEventInterface;

final class WorkerExecuteParallelInitEvent
{
    public function __construct(
        readonly private ScheduleEventInterface $scheduleEvent,
    ) {
    }

    public function getScheduleEvent(): ScheduleEventInterface
    {
        return $this->scheduleEvent;
    }
}
