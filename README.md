# Smibo Task Scheduler Bundle

This is a simple bundle for task management. Can be very useful if you are using some message brokers. 

## Installation

```
composer require smibo/task-scheduler-bundle 
```

```php
<?php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Smibo\Bundle\TaskSchedulerBundle\SmiboTaskSchedulerBundle(),
            
        ];
        // ...
    }
    // ...
}
```

## Get started  

### Config example

```yaml
smibo_task_scheduler:
    storage: task_storage #required
    default_handler: default_handler #required
    default_checker: default_checker #optional
    default_interval: PT30M #required
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
Just simple custom [Value Object](https://en.wikipedia.org/wiki/Value_object) which implements TaskInterface. 

#### Example
```php
<?php
declare(strict_types=1);

namespace AppBundle\Tasks;

use Smibo\Bundle\TaskSchedulerBundle\TaskInterface;

class SendMailToVIPCustomerTask implements TaskInterface
{
    /**
     * @var string
     */
    protected $message;

    /**
     * SendMailToVIPCustomerTask constructor.
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
```

### Storage
Service implements Smibo\Bundle\TaskSchedulerBundle\StorageInterface. 
Allows to check when was the task's last run by the Handler.

#### Example
```php
<?php
declare(strict_types=1);

namespace AppBundle;

use Smibo\Bundle\TaskSchedulerBundle\StorageInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class TaskDataStorage implements ContainerAwareInterface, StorageInterface
{
    use ContainerAwareTrait;

    /**
     * @param string $id
     * @return \DateTime
     * @throws \Exception
     */
    public function getTaskLastRunTime(string $id, \DateInterval $interval): \DateTime
    {
        $em = $this->container->get('doctrine')->getManager();
        if ($task = $em->getRepository(AppBundle\Entity\Task::class)->findBy($id)) {
            
            return $task->getLastRunDate();
        }
        
        return (new DateTime())->sub($interval)->sub(new \DateInterval('PT1S'));
    }

    /**
     * @param string $id
     * @param \DateTime $time
     * @throws \Exception
     */
    public function saveTaskLastRunTime(string $id, \DateTime $time): void
    {
        $em = $this->container->get('doctrine')->getManager();
        if (!$task = $em->getRepository(AppBundle\Entity\Task::class)->findBy($id)) {
            throw new \Exception("Can`t find task {$id}");
        }
        $task->setLastRunDate($time);
        $em->persist($task);
        $em->flush();
    }
}
```

```yaml
services:
    task_storage:
        class: AppBundle\TaskDataStorage
        calls:
            - [setContainer, ['@service_container']]
```

### Handler
Service implements Smibo\Bundle\TaskSchedulerBundle\HandlerInterface. 
Handle the task and process it.

#### Example

```php
<?php
declare(strict_types=1);

namespace AppBundle\TaskHandlers;

use Smibo\Bundle\TaskSchedulerBundle\HandlerInterface;
use Smibo\Bundle\TaskSchedulerBundle\TaskInterface;

class DefaultTaskHandler implements HandlerInterface
{
    /**
     * @param string $id
     * @param TaskInterface $task
     */
    function handle(string $id, TaskInterface $task): void
    {
        var_dump($task);
    }
}
```

```yaml
services:
    default_task_handler:
        class: AppBundle\TaskHandlers\DefaultTaskHandler
```

### Checker
Service implements Smibo\Bundle\TaskSchedulerBundle\CheckerInterface. 
 Checks if the tasks are ready and can be passed to the Handler service. Optional stage, since as a rule it is enough to set Interval.

#### Example

```php
<?php
declare(strict_types=1);

namespace AppBundle\TaskCheckers;

use Smibo\Bundle\TaskSchedulerBundle\CheckerInterface;
use Smibo\Bundle\TaskSchedulerBundle\TaskInterface;

class DefaultTaskChecker implements CheckerInterface
{
    /**
     * @param string $id 
     * @param TaskInterface $task
     * @return bool
     */
    function check(string $id, TaskInterface $task): bool
    {
        return (boolean) rand(0,1);
    }
}
```

```yaml
services:
    default_task_checker:
        class: AppBundle\TaskHandlers\DefaultChecker
```

### Interval
Just [DateInterval](http://php.net/manual/ru/class.dateinterval.php) value.

### Events
Also you can simple use [Events and Event Listeners](http://symfony.com/doc/current/event_dispatcher.html) with the next events:
   * AfterTaskSchedulerHandleTaskEvent::NAME ('after.task.scheduler.handle.task')
   * AfterTaskSchedulerRunEvent::NAME ('after.task.scheduler.run')
   * BeforeTaskSchedulerHandleTaskEvent::NAME ('before.task.scheduler.handle.task')
   * BeforeTaskSchedulerRunEvent::NAME ('before.task.scheduler.run')
   * TaskSchedulerExceptionEvent::NAME ('task.scheduler.exception')

## Using

```
./bin/console task-scheduler:run
```
Good idea will be to create a [unix daemon](https://en.wikipedia.org/wiki/Daemon_(computing)) and control it by some supervisor like [supervisord](http://supervisord.org)  