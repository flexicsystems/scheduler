<?php

namespace Flexic\Scheduler\DateTime;

class Timezone
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