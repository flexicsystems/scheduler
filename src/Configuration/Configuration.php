<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 ThemePoint
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace ThemePoint\Scheduler\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class Configuration
{
    protected function resolve(
        array $options,
        null|array $defaults = null,
    ): array {
        $resolver = new OptionsResolver();

        if (\is_array($defaults)) {
            $resolver->setDefaults($defaults);
        }

        return $resolver->resolve($options);
    }
}
