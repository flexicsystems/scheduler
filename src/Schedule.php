<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 2.0.0
 */

namespace Flexic\Scheduler;

use Cron;
use Flexic\Scheduler\Factory\CronExpressionFactory;
use Flexic\Scheduler\Interfaces\ScheduleInterface;

final class Schedule implements ScheduleInterface
{
    private Cron\CronExpression $cron;

    private \DateTimeZone $timezone;

    public function __construct()
    {
        $this->cron = CronExpressionFactory::create();
        $this->timezone = new \DateTimeZone(\date_default_timezone_get());
    }

    public function cron(string $expression): self
    {
        $this->cron = CronExpressionFactory::create($expression);

        return $this;
    }

    public function timezone(string $timeZone): self
    {
        if (!\in_array($timeZone, \timezone_identifiers_list(), true)) {
            throw new \RuntimeException(\sprintf('Timezone %s is not valid', $timeZone));
        }

        $this->timezone = new \DateTimeZone($timeZone);

        return $this;
    }

    public function getTimezone(): \DateTimeZone
    {
        return $this->timezone;
    }

    public function getExpression(): Cron\CronExpression
    {
        return $this->cron;
    }
}
