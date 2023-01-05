🕧 PHP Scheduler
----------------

PHP Schedule is a simple library for scheduling tasks in PHP.   
It is inspired by the [Laravel Scheduler](https://laravel.com/docs/scheduling) and [Symfony Messenger](https://symfony.com/doc/current/components/messenger.html).

----
### Installation

Run

```bash
composer require flexic/scheduler
```

to install `flexic/scheduler`.

----
### Setup Events to schedule

```php
class MyScheduleEvent implements \Flexic\Scheduler\Interfaces\ScheduleEventInterface
{
    public function __invoke(): void
    {
        // ... do something
    }
    
    public function configure(Flexic\Scheduler\Interfaces\ScheduleInterface $schedule): void
    {
        $schedule->cron('* * * * *');
    }
}
```

Schedule events are classes that implement the `ScheduleEventInterface`.
Inside the `configure` method, you can use the `Schedule` object to define when the event should be scheduled to run.

### Setup Schedule Worker (Console Command)

Run
```bash
php bin/schedule ./path/to/event_config.php ./path/to/event_config_1.php
```
to start the schedule worker. Worker will automatically load all events from the given config files and run them.

#### Options
| Option        |                                              Description                                               |    Format     | Default |
|---------------|:------------------------------------------------------------------------------------------------------:|:-------------:|:-------:|
| limit         | Limits the worker to the give number. Worker stops automatically if number if max Event runs exceeded. |      int      |    -    |
| timeLimit     |                              Worker stops automatically after given time.                              | int (seconds) |    -    |
| intervalLimit |                       Worker stops automatically after give amount of intervals.                       |      int      |    -    |
| memoryLimit   |                 Worker stops automatically if usage of memory exceeds the given limit.                 |  int (bytes)  |    -    |
| parallel      |                             Worker runs events in parallel if set to true.                             |    boolean    |  false  |


### Setup Schedule Worker (own script)

```php
# Options for worker
$options = [];
$events = [
    new MyScheduleEvent(),
];

$worker = new \Flexic\Scheduler\Worker(
    new Flexic\Scheduler\Configuration\WorkerConfiguration($options),
    $events,
    new \Symfony\Component\EventDispatcher\EventDispatcher(),
);

$worker->start();
```

### ScheduleEvent Factory
The `ScheduleEventInterface` is implemented to allow the usage of a factory to create the event. This is useful if you want to use a dependency injection container to create the event.

```php
class MyScheduleEventFactory implements \Flexic\Scheduler\Interfaces\ScheduleEventFactoryInterface
{
    public function create(): array {
        return [
            new MyScheduleEvent('foo'),
            new MyScheduleEvent('bar'),
        ];
    }
}
```

### Schedule API
| Method                          | Description                                    |
|---------------------------------|------------------------------------------------|
| cron($expression)               | Schedule the event on a Cron expression.       |
| timezone($timezone)             | Set the timezone the expression should run in. |
| minute($miute)                  | Set minutes to cron expression                 |
| minutes($miutes)                | Set a list of minutes to cron expression       |
| minutesBetween($start, $end)    | Set a range of minutes to cron expression      |
| hour($hour)                     | Set hours to cron expression                   |
| hours($hours)                   | Set a list of hours to cron expression         |
| hoursBetween($start, $end)      | Set a range of hours to cron expression        |
| day($day)                       | Set days to cron expression                    |
| days($days)                     | Set a list of days to cron expression          |
| daysBetween($start, $end)       | Set a range of days to cron expression         |
| month($month)                   | Set months to cron expression                  |
| months($months)                 | Set a list of months to cron expression        |
| monthsBetween($start, $end)     | Set a range of months to cron expression       |
| dayOfWeek($day)                 | Set days of week to cron expression            |
| daysOfWeek($days)               | Set a list of days of week to cron expression  |
| daysOfWeekBetween($start, $end) | Set a range of days of week to cron expression |




### Worker API
| Method    |                     Description                      |
|-----------|:----------------------------------------------------:|
| start()   |                  Starts the worker.                  |
| stop()    |                  Stops the worker.                   |
| restart() |        Reinitialize and restarts the worker.         |
| update()  | Update the worker and starts with new configuration. |

### Worker Lifecycle Events
| Event Name                       | Description                                               |
|----------------------------------|-----------------------------------------------------------|
| **Worker Lifecycle**             | Flexic\Scheduler\Event\Event\Lifecycle\\\<EventName>      |
| WorkerInitializedEvent           | Executed when worker is initialized.                      |
| WorkerStartEvent                 | Executed when worker is started.                          |
| WorkerStopEvent                  | Executed when worker is stopped.                          |
| WorkerRestartEvent               | Executed when worker is restarted.                        |
| WorkerUpdateEvent                | Executed everytime the worker is updated.                 |
|                                  |
| **Run Lifecycle**                | Flexic\Scheduler\Event\Event\Run\\\<EventName>            |
| WorkerRunStartEvent              | Executed everytime an event is started to process.        |
| WorkerRunEnvEvent                | Executed everytime an event is finished to process.       |
|                                  |
| **Interval Lifecycle**           | Flexic\Scheduler\Event\Event\Interval\\\<EventName>       |
| WorkerIntervalStartEvent         | Executed everytime a interval is started.                 |
| WorkerIntervalEndEvent           | Executed everytime a interval is finished.                |
|                                  |
| **Execution Lifecycle**          | Flexic\Scheduler\Event\Event\Execute\\\<EventName>        |
| WorkerExecuteEvent               | Executed everytime an event is executed.                  |
| WorkerExecuteSequentialEvent     | Executed everytime an event is executed sequentially.     |
| WorkerExecuteParallelStartEvent  | Executed everytime an event is executed parallel.         |
| WorkerExecuteParallelResumeEvent | Executed everytime an parallel executed event is resumed. |

----
### License
This package is licensed using the GNU License.

Please have a look at [LICENSE.md](LICENSE.md).

----

[![Donate](https://img.shields.io/badge/Donate-PayPal-blue.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Q98R2QXXMTUF6&source=url)