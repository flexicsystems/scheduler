<?php

namespace Flexic\Scheduler\Interfaces;

interface ScheduleEventPrototypeInterface
{
    public function __invoke(): void;

    /**
     * Configure the schedule for schedule event.
     */
    public function configure(ScheduleInterface $schedule): void;
}