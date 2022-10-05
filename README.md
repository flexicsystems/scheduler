🕧 PHP Schedule
----------------

PHP Schedule is a simple library for scheduling tasks in PHP.   
It is inspired by the [Laravel Scheduler](https://laravel.com/docs/scheduling) and [Symfony Messenger](https://symfony.com/doc/current/components/messenger.html).

----
### Installation

Run

```bash
composer require themepoint/scheduler
```

to install `themepoint/scheduler`.

----
### Setup Events to schedule

```php
class MyScheduleEvent implements \ThemePoint\Scheduler\Interfaces\ScheduleEventInterface
{
    public function __invoke(): void
    {
        // ... do something
    }
    
    public function configure(ThemePoint\Scheduler\Interfaces\ScheduleInterface $schedule): void
    {
        $schedule->cron('* * * * *');
    }
}
```

Schedule events are classes that implement the `ScheduleEventInterface`.
Inside the `configure` method, you can use the `Schedule` object to define when the event should be scheduled to run.

### Setup Schedule Worker (own script)

```php
# Options for worker
$options = [];
$events = [
    new MyScheduleEvent(),
];

$worker = new \ThemePoint\Scheduler\Worker(
    new ThemePoint\Scheduler\Configuration\WorkerConfiguration($options),
    $events,
    new \Symfony\Component\EventDispatcher\EventDispatcher(),
);

$worker->run();
```

----
### License
This package is licensed using the GNU License.

Please have a look at [LICENSE.md](LICENSE.md).

----

### Changelog
[1.0][2022-10-05] Initial release

---

[![Donate](https://img.shields.io/badge/Donate-PayPal-blue.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Q98R2QXXMTUF6&source=url)