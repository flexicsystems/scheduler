<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Logger;

final class Logger
{
    public function __construct(
        readonly private null|\Symfony\Component\Console\Style\SymfonyStyle $io = null,
        readonly private null|\Psr\Log\LoggerInterface $logger = null,
    ) {
    }

    public function info(string $message, array $context = []): void
    {
        $message = $this->format($message);

        $this->io?->info($message);
        $this->logger?->info($message, $context);
    }

    public function success(string $message, array $context = []): void
    {
        $message = $this->format($message);

        $this->io?->success($message);
        $this->logger?->info($message, $context);
    }

    private function format(string $message): string
    {
        return \sprintf('[ScheduleWorker] %s', $message);
    }
}
