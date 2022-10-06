<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 ThemePoint
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace ThemePoint\Scheduler;

final class Util
{
    public static function isValidTimeZone(string $timeZone): bool
    {
        return \in_array($timeZone, \timezone_identifiers_list(), true);
    }
}
