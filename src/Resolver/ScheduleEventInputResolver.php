<?php

namespace Flexic\Scheduler\Resolver;

use Flexic\Scheduler\Interfaces\ScheduleEventFactoryInterface;
use Flexic\Scheduler\Interfaces\ScheduleEventInterface;

class ScheduleEventInputResolver
{
    /**
     * @param array<ScheduleEventInterface|ScheduleEventFactoryInterface> $input
     * @return array<ScheduleEventInterface>
     */
    public function resolve(array $input): array
    {
        $events = [];

        foreach ($input as $item) {
            if ($item instanceof ScheduleEventInterface) {
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