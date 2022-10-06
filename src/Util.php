<?php

namespace ThemePoint\Scheduler;

class Util
{
    public static function isValidTimeZone(string $timeZone): bool
    {
        return \in_array($timeZone, \timezone_identifiers_list(), true);
    }
}