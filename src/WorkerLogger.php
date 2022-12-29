<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler;

final class WorkerLogger
{
    public function __construct(
        private null|\Symfony\Component\Console\Style\SymfonyStyle $io = null,
        private null|\Psr\Log\LoggerInterface $logger = null,
    ) {
    }

    public function info(string $message, array $context = []): void
    {
        $this->io?->info($message);
        $this->logger?->info($message, $context);
    }

    public function success(string $message, array $context = []): void
    {
        $this->io?->success($message);
        $this->logger?->info($message, $context);
    }
}
