<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Resolver;

use Flexic\Scheduler\Interfaces\ScheduleEventFactoryInterface;
use Flexic\Scheduler\Interfaces\ScheduleEventInterface;

final class ScheduleEventFileResolver
{
    public function resolve(iterable $loaded, iterable $files): array
    {
        $loaded = $this->iterableToArray($loaded);

        foreach ($files as $file) {
            \array_push($loaded, ...$this->load($file));
        }

        return $loaded;
    }

    private function load(string $file): array
    {
        $path = \realpath(\sprintf('%s/%s', \getcwd(), $file));

        if (false === $path) {
            throw new \RuntimeException(\sprintf('Schedule event file "%s" not found.', $file));
        }

        $configuration = require $path;

        if ($configuration instanceof ScheduleEventInterface || $configuration instanceof ScheduleEventFactoryInterface) {
            return [$configuration];
        }

        if (\is_array($configuration)) {
            return \array_filter($configuration, static function ($event): bool {
                return $event instanceof ScheduleEventInterface || $event instanceof ScheduleEventFactoryInterface;
            });
        }

        return [];
    }

    private function iterableToArray(iterable $iterable): array
    {
        $array = [];
        \array_push($array, ...$iterable);

        return $array;
    }
}
