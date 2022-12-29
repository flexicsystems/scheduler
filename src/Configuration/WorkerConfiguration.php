<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Configuration;

use Flexic\Scheduler\Constants\WorkerOptions;
use Flexic\Scheduler\Logger\Logger;
use Flexic\Scheduler\Worker;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class WorkerConfiguration
{
    public Worker $worker;

    public Logger $logger;

    private readonly array $options;

    public function __construct(
        array $options = [],
        ?SymfonyStyle $io = null,
        ?LoggerInterface $logger = null,
    ) {
        $resolver = new OptionsResolver();

        $resolver->setDefaults(WorkerOptions::DEFAULTS);

        $this->options = $resolver->resolve($options);

        $this->logger = new Logger(
            $io,
            $logger,
        );
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    public function setWorker(Worker $worker): void
    {
        $this->worker = $worker;
    }

    public function getWorker(): Worker
    {
        return $this->worker;
    }

    public function getOption(string $option): mixed
    {
        if (!\array_key_exists($option, $this->options)) {
            throw new \InvalidArgumentException(\sprintf('The option "%s" does not exist.', $option));
        }

        return $this->options[$option];
    }
}
