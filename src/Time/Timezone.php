<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Time;

final class Timezone
{
    private string $defaultTimezone;

    public function __construct()
    {
        $this->defaultTimezone = \date_default_timezone_get();
    }

    public function set(\DateTimeZone $timezone): void
    {
        \date_default_timezone_set($timezone->getName());
    }

    public function default(): void
    {
        \date_default_timezone_set($this->defaultTimezone);
    }
}
