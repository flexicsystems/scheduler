<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 2.0.0
 */

namespace Flexic\Scheduler\Command;

use Flexic\Scheduler\Configuration\WorkerConfiguration;
use Flexic\Scheduler\Constants\WorkerOptions;
use Flexic\Scheduler\Resolver\ScheduleEventFileResolver;
use Flexic\Scheduler\Worker;
use Symfony\Component\Console;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class RunWorkerCommand extends Console\Command\Command
{
    public const COMMAND_NAME = 'scheduler:run-worker';

    private readonly EventDispatcher $eventDispatcher;

    private iterable $scheduleEvents;

    public function __construct(
        ?EventDispatcher $eventDispatcher = null,
        iterable $scheduleEvents = [],
    ) {
        parent::__construct(self::COMMAND_NAME);

        $this->scheduleEvents = $scheduleEvents;
        $this->eventDispatcher = $eventDispatcher ?? new EventDispatcher();
    }

    protected function configure(): void
    {
        $this->setDescription('Runs the scheduler worker.');
        $this->addOption(
            WorkerOptions::SCHEDULE_EVENT_LIMIT,
            null,
            Console\Input\InputOption::VALUE_REQUIRED,
            'The maximum number of events to run.',
            null,
        );
        $this->addOption(
            WorkerOptions::INTERVAL_LIMIT,
            null,
            Console\Input\InputOption::VALUE_REQUIRED,
            'The maximum number of interval to run.',
            null,
        );
        $this->addOption(
            WorkerOptions::TIME_LIMIT,
            null,
            Console\Input\InputOption::VALUE_REQUIRED,
            'The maximum time to run.',
            null,
        );
        $this->addOption(
            WorkerOptions::MEMORY_LIMIT,
            null,
            Console\Input\InputOption::VALUE_REQUIRED,
            'The maximum memory to run.',
            null,
        );
        $this->addOption(
            WorkerOptions::PARALLEL_EXECUTION,
            null,
            Console\Input\InputOption::VALUE_NONE,
            'Allow worker to run events parallel.',
            null,
        );
        $this->addOption(
            WorkerOptions::PARALLEL_EXECUTION_LIMIT,
            null,
            Console\Input\InputOption::VALUE_REQUIRED,
            'The maximum number of parallel events to run.',
            0,
        );
        $this->addArgument(
            'schedule-event',
            Console\Input\InputArgument::IS_ARRAY,
            'The schedule events to run.',
            [],
        );
    }

    protected function execute(
        Console\Input\InputInterface $input,
        Console\Output\OutputInterface $output,
    ): int {
        $io = new SymfonyStyle($input, $output);

        $configuration = new WorkerConfiguration(
            [
                WorkerOptions::SCHEDULE_EVENT_LIMIT => $input->getOption(WorkerOptions::SCHEDULE_EVENT_LIMIT),
                WorkerOptions::INTERVAL_LIMIT => $input->getOption(WorkerOptions::INTERVAL_LIMIT),
                WorkerOptions::TIME_LIMIT => $input->getOption(WorkerOptions::TIME_LIMIT),
                WorkerOptions::MEMORY_LIMIT => $input->getOption(WorkerOptions::MEMORY_LIMIT),
                WorkerOptions::PARALLEL_EXECUTION => $input->getOption(WorkerOptions::PARALLEL_EXECUTION),
                WorkerOptions::PARALLEL_EXECUTION_LIMIT => $input->getOption(WorkerOptions::PARALLEL_EXECUTION_LIMIT),
            ],
            $io,
        );

        $eventFiles = $input->getArgument('schedule-event');

        $scheduleEvents = (new ScheduleEventFileResolver())->resolve(
            $this->scheduleEvents,
            \is_array($eventFiles) ? $eventFiles : [],
        );

        $worker = new Worker(
            $configuration,
            $scheduleEvents,
            $this->eventDispatcher,
        );

        $worker->start();

        return Console\Command\Command::SUCCESS;
    }
}
