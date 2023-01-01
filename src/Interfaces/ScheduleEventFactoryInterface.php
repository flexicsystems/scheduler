<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Interfaces;

interface ScheduleEventFactoryInterface
{
    /**
     * @return ScheduleEventInterface[]
     */
    public function create(): array;
}
