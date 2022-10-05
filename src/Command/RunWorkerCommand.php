<?php

namespace ThemePoint\Scheduler\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Style\SymfonyStyle as Cli;
use Symfony\Component\EventDispatcher\EventDispatcher;
use ThemePoint\Scheduler\Configuration\WorkerConfiguration;
use ThemePoint\Scheduler\Constants\WorkerOptions;
use ThemePoint\Scheduler\Interfaces\ScheduleEventInterface;
use ThemePoint\Scheduler\Worker;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunWorkerCommand extends Console\Command\Command
{
    public const COMMAND_NAME = 'scheduler:run-worker';

    readonly private EventDispatcher $eventDispatcher;

    private ?array $scheduleEvents;

    public function __construct(
        EventDispatcher $eventDispatcher = null,
        array $scheduleEvents = []
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
            null
        );
        $this->addOption(
            WorkerOptions::INTERVAL_LIMIT,
            null,
            Console\Input\InputOption::VALUE_REQUIRED,
            'The maximum number of interval to run.',
            null
        );
        $this->addOption(
            WorkerOptions::TIME_LIMIT,
            null,
            Console\Input\InputOption::VALUE_REQUIRED,
            'The maximum time to run.',
            null
        );
        $this->addOption(
            WorkerOptions::MEMORY_LIMIT,
            null,
            Console\Input\InputOption::VALUE_REQUIRED,
            'The maximum memory to run.',
            null
        );
        $this->addArgument(
            'schedule-event',
            Console\Input\InputArgument::IS_ARRAY,
            'The schedule events to run.',
            []
        );
    }

    protected function execute(
        Console\Input\InputInterface $input,
        Console\Output\OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $this->registerScheduleEvents(
            $input->getArgument('schedule-event'),
            $io
        );

        $configuration = new WorkerConfiguration([
            WorkerOptions::SCHEDULE_EVENT_LIMIT => $input->getOption(WorkerOptions::SCHEDULE_EVENT_LIMIT),
            WorkerOptions::INTERVAL_LIMIT => $input->getOption(WorkerOptions::INTERVAL_LIMIT),
            WorkerOptions::TIME_LIMIT => $input->getOption(WorkerOptions::TIME_LIMIT),
            WorkerOptions::MEMORY_LIMIT => $input->getOption(WorkerOptions::MEMORY_LIMIT),
        ], $io);

        $worker = new Worker(
            $configuration,
            $this->scheduleEvents,
            $this->eventDispatcher,
        );

        $worker->run();

        return Console\Command\Command::SUCCESS;
    }

    private function registerScheduleEvents(
        array $scheduleEvents,
        SymfonyStyle $io
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
                    \array_map(function (ScheduleEventInterface $event) use (&$events) {
                        $events[] = $event;
                    }, \array_filter($configuration, function ($event): bool {
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