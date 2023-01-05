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

    public function minute(string|int $minute): self
    {
        $this->cron->setPart(Cron\CronExpression::MINUTE, (string) $minute);

        return $this;
    }

    public function minutes(string|int ...$minutes): self
    {
        $this->cron->setPart(Cron\CronExpression::MINUTE, (string) \implode(',', $minutes));

        return $this;
    }

    public function minutesBetween(string|int $start, string|int $end): self
    {
        $this->cron->setPart(Cron\CronExpression::MINUTE, (string) \sprintf('%s-%s', $start, $end));

        return $this;
    }

    public function hour(string|int $hour): self
    {
        $this->cron->setPart(Cron\CronExpression::HOUR, (string) $hour);

        return $this;
    }

    public function hours(string|int ...$hours): self
    {
        $this->cron->setPart(Cron\CronExpression::HOUR, (string) \implode(',', $hours));

        return $this;
    }

    public function hoursBetween(string|int $start, string|int $end): self
    {
        $this->cron->setPart(Cron\CronExpression::HOUR, (string) \sprintf('%s-%s', $start, $end));

        return $this;
    }

    public function day(string|int $day): self
    {
        $this->cron->setPart(Cron\CronExpression::DAY, (string) $day);

        return $this;
    }

    public function days(string|int ...$days): self
    {
        $this->cron->setPart(Cron\CronExpression::DAY, (string) \implode(',', $days));

        return $this;
    }

    public function daysBetween(string|int $start, string|int $end): self
    {
        $this->cron->setPart(Cron\CronExpression::DAY, (string) \sprintf('%s-%s', $start, $end));

        return $this;
    }

    public function month(string|int $month): self
    {
        $this->cron->setPart(Cron\CronExpression::MONTH, (string) $month);

        return $this;
    }

    public function months(string|int ...$months): self
    {
        $this->cron->setPart(Cron\CronExpression::MONTH, (string) \implode(',', $months));

        return $this;
    }

    public function monthsBetween(string|int $start, string|int $end): self
    {
        $this->cron->setPart(Cron\CronExpression::MONTH, (string) \sprintf('%s-%s', $start, $end));

        return $this;
    }

    public function dayOfWeek(string|int $dayOfWeek): self
    {
        $this->cron->setPart(Cron\CronExpression::WEEKDAY, (string) $dayOfWeek);

        return $this;
    }

    public function daysOfWeek(string|int ...$daysOfWeek): self
    {
        $this->cron->setPart(Cron\CronExpression::WEEKDAY, (string) \implode(',', $daysOfWeek));

        return $this;
    }

    public function daysOfWeekBetween(string|int $start, string|int $end): self
    {
        $this->cron->setPart(Cron\CronExpression::WEEKDAY, (string) \sprintf('%s-%s', $start, $end));

        return $this;
    }
}
