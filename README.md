# Smibo Task Scheduler Bundle

This is a simple bundle for task management. Can be very useful if you are using some message brokers. 

## Installation

```
composer require smibo/task-scheduler-bundle 
```

## Get started  

### Config example

```yaml
smibo_task_scheduler:
    storage: db_task_time_storage
    default_handler: default_handler
    default_checker: default_checker
    default_interval: PT30M
    tasks:
        import:
            class: AppBundle\Task\Import #required
            handler: import_handler #optional
            checker: import_checker #optional
            arguments: #optional
                supplier: veryCoolSupplier
        sendMail:
            class: AppBundle\Task\Mail
            interval: PT15M #optional
            arguments:
                from: somebody@mail.com
                to: anotherbody@mail.com
```
### Task
Just simple custom [Value Object](https://en.wikipedia.org/wiki/Value_object) which does not implement any task-cheduler-interface 

### Storage
Service implements Smibo\TaskSchedulerBundle\StorageInterface. 
Allows to check when was the task's last run by the Handler.

### Handler
Service implements Smibo\TaskSchedulerBundle\HandlerInterface. 
Processes the task.

### Checker
Service implements Smibo\TaskSchedulerBundle\CheckerInterface. 
 Checks if the tasks are ready and can be passed to the Handler service. Optional stage, since as a rule it is enough to set Interval.

### Interval
Just [DateInterval](http://php.net/manual/ru/class.dateinterval.php) value.


## Using
```
./bin/console task-scheduler:run
```
Good idea will be to crate a unix daemon and control it by some supervisor like [supervisord](http://supervisord.org)  