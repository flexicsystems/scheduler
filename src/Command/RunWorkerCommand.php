<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2022 ThemePoint
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\Scheduler\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Flexic\Scheduler\Configuration\WorkerConfiguration;
use Flexic\Scheduler\Constants\WorkerOptions;
use Flexic\Scheduler\Interfaces\ScheduleEventInterface;
use Flexic\Scheduler\Worker;

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

        $this->registerScheduleEvents(
            (array) $input->getArgument('schedule-event'),
            $io,
        );

        $configuration = new WorkerConfiguration([
            WorkerOptions::SCHEDULE_EVENT_LIMIT => $input->getOption(WorkerOptions::SCHEDULE_EVENT_LIMIT),
            WorkerOptions::INTERVAL_LIMIT => $input->getOption(WorkerOptions::INTERVAL_LIMIT),
            WorkerOptions::TIME_LIMIT => $input->getOption(WorkerOptions::TIME_LIMIT),
            WorkerOptions::MEMORY_LIMIT => $input->getOption(WorkerOptions::MEMORY_LIMIT),
        ], $io);

        $scheduleEvents = [];
        \array_push($scheduleEvents, ...$this->scheduleEvents);

        $worker = new Worker(
            $configuration,
            $scheduleEvents,
            $this->eventDispatcher,
        );

        $worker->run();

        return Console\Command\Command::SUCCESS;
    }

    private function registerScheduleEvents(
        array $scheduleEvents,
        SymfonyStyle $io,
    ): void {
        if (\count($scheduleEvents) <= 0) {
            if (\count($this->scheduleEvents) <= 0) {
                $io->error('No schedule events found.');

                exit(1);
            }

            return;
        }

        $events = [];

        foreach ($scheduleEvents as $eventFile) {
            try {
                $path = \realpath(\sprintf('%s/%s', \getcwd(), $eventFile));

                if (false === $path) {
                    $io->error(\sprintf('Schedule event file "%s" not found.', $eventFile));

                    continue;
                }

                $configuration = require_once $path;

                if ($configuration instanceof ScheduleEventInterface) {
                    $events[] = $configuration;
                }

                if (\is_array($configuration)) {
                    \array_map(static function (ScheduleEventInterface $event) use (&$events): void {
                        $events[] = $event;
                    }, \array_filter($configuration, static function ($event): bool {
                        return $event instanceof ScheduleEventInterface;
                    }));
                }
            } catch (\Exception $exception) {
                $io->error(\sprintf('Unexpected error while load event file: %s', $exception->getMessage()));

                continue;
            }
        }

        $this->scheduleEvents = $events;
    }
}
