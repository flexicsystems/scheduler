<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 2.0.0
 */

namespace Flexic\Scheduler\Constants;

final class WorkerOptions
{
    public const INTERVAL_LIMIT = 'intervalLimit';

    public const SCHEDULE_EVENT_LIMIT = 'limit';

    public const TIME_LIMIT = 'timeLimit';

    public const MEMORY_LIMIT = 'memoryLimit';

    public const PARALLEL_EXECUTION = 'parallel';

    public const PARALLEL_EXECUTION_LIMIT = 'parallelLimit';

    public const DEFAULTS = [
        self::SCHEDULE_EVENT_LIMIT => null,
        self::TIME_LIMIT => null,
        self::MEMORY_LIMIT => null,
        self::INTERVAL_LIMIT => null,
        self::PARALLEL_EXECUTION => false,
        self::PARALLEL_EXECUTION_LIMIT => null,
    ];
}
