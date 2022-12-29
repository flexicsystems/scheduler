<?php

namespace Flexic\Scheduler;

class WorkerLogger
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