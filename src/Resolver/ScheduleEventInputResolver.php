<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 2.0.0
 */

namespace Flexic\Scheduler\Resolver;

use Flexic\Scheduler\Interfaces\AbstractScheduleEventInterface;
use Flexic\Scheduler\Interfaces\ScheduleEventFactoryInterface;

final class ScheduleEventInputResolver
{
    /**
     * @param array<AbstractScheduleEventInterface|ScheduleEventFactoryInterface> $input
     *
     * @return array<AbstractScheduleEventInterface>
     */
    public function resolve(array $input): array
    {
        $events = [];

        foreach ($input as $item) {
            if ($item instanceof AbstractScheduleEventInterface) {
                $events[] = $item;
            }

            if ($item instanceof ScheduleEventFactoryInterface) {
                foreach ($item->create() as $factoryItem) {
                    $events[] = $factoryItem;
                }
            }
        }

        return $events;
    }
}
