<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Constants;

final class WorkerOptions
{
    public const INTERVAL_LIMIT = 'intervalLimit';

    public const SCHEDULE_EVENT_LIMIT = 'limit';

    public const TIME_LIMIT = 'timeLimit';

    public const MEMORY_LIMIT = 'memoryLimit';

    public const PARALLEL_EXECUTION = 'parallel';

    public const DEFAULTS = [
        WorkerOptions::SCHEDULE_EVENT_LIMIT => null,
        WorkerOptions::TIME_LIMIT => null,
        WorkerOptions::MEMORY_LIMIT => null,
        WorkerOptions::INTERVAL_LIMIT => null,
        WorkerOptions::PARALLEL_EXECUTION => false,
    ];
}
