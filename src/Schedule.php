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
use Flexic\CronBuilder;
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

    public function cron(CronBuilder\CronBuilder|CronBuilder\Cron|string $expression): self
    {
        if ($expression instanceof CronBuilder\CronBuilder) {
            $expression = $expression->build();
        }

        $this->cron = CronExpressionFactory::create((string) $expression);

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

    public function minute(CronBuilder\Expression\ExpressionInterface|string|int $minute): self
    {
        $this->cron->setPart(Cron\CronExpression::MINUTE, (string) $minute);

        return $this;
    }

    public function hour(CronBuilder\Expression\ExpressionInterface|string|int $hour): self
    {
        $this->cron->setPart(Cron\CronExpression::HOUR, (string) $hour);

        return $this;
    }

    public function day(CronBuilder\Expression\ExpressionInterface|string|int $day): self
    {
        $this->cron->setPart(Cron\CronExpression::DAY, (string) $day);

        return $this;
    }

    public function month(CronBuilder\Expression\ExpressionInterface|string|int $month): self
    {
        $this->cron->setPart(Cron\CronExpression::MONTH, (string) $month);

        return $this;
    }

    public function dayOfWeek(CronBuilder\Expression\ExpressionInterface|string|int $dayOfWeek): self
    {
        $this->cron->setPart(Cron\CronExpression::WEEKDAY, (string) $dayOfWeek);

        return $this;
    }

    public function builder(): CronBuilder\CronBuilder
    {
        return CronBuilder\CronBuilder::create();
    }
}
