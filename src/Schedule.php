<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler;

use Cron;
use Flexic\Scheduler\Factory\CronExpressionFactory;
use Flexic\Scheduler\Interfaces\ScheduleInterface;

final class Schedule implements ScheduleInterface
{
    private Cron\CronExpression $cron;

    public function __construct()
    {
        $this->cron = CronExpressionFactory::create();
    }

    public function cron(string $expression): self
    {
        $this->cron = CronExpressionFactory::create($expression);

        return $this;
    }

    public function getExpression(): Cron\CronExpression
    {
        return $this->cron;
    }
}
