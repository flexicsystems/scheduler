<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 2.0.0
 */

namespace Flexic\Scheduler\Time;

final class Timer
{
    private const NEXT_TICK = '+ 1 minute';

    public function waitForNextTick(): void
    {
        \sleep($this->getTimeUntilNextTick());
    }

    private function getTimeUntilNextTick(): int
    {
        $actual = (new \DateTimeImmutable())->setTimestamp(\time());
        $next = $actual->setTime(
            (int) $actual->format('H'),
            (int) $actual->modify(self::NEXT_TICK)->format('i'),
            0,
        );

        $time = $next->getTimestamp() - $actual->getTimestamp();

        if (0 > $time) {
            return 0;
        }

        return $time;
    }
}
