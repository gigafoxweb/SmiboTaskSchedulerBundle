<?php declare(strict_types=1);

namespace Smibo\Bundle\TaskSchedulerBundle;


use DateInterval;
use DateTime;
use Smibo\Bundle\TaskSchedulerBundle\Events\AfterTaskSchedulerHandleTaskEvent;
use Smibo\Bundle\TaskSchedulerBundle\Events\AfterTaskSchedulerRunEvent;
use Smibo\Bundle\TaskSchedulerBundle\Events\BeforeTaskSchedulerHandleTaskEvent;
use Smibo\Bundle\TaskSchedulerBundle\Events\BeforeTaskSchedulerRunEvent;
use Smibo\Bundle\TaskSchedulerBundle\Events\SchedulerEvent;
use Smibo\Bundle\TaskSchedulerBundle\Events\TaskSchedulerExceptionEvent;
use Smibo\Bundle\TaskSchedulerBundle\Exceptions\TaskSchedulerException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TaskScheduler
{
    /**
     * @var TaskManager
     */
    protected $taskManager;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * TaskScheduler constructor.
     * @param TaskManager $taskManager
     * @param StorageInterface $storage
     */
    public function __construct(TaskManager $taskManager, StorageInterface $storage)
    {
        $this->taskManager = $taskManager;
        $this->storage = $storage;
    }

    /**
     * @throws TaskSchedulerException
     * @return void
     */
    public function run(): void
    {
        $this->dispatchEvent(BeforeTaskSchedulerRunEvent::NAME, new BeforeTaskSchedulerRunEvent($this));
        try {
            foreach ($this->taskManager->getTasks() as $id => $task) {
                if ($task instanceof TaskSchedulerContainer) {
                    $now = new DateTime();
                    if ($this->storage->getTaskLastRunTime($id, $task->getInterval())->add($task->getInterval()) > $now) {
                        continue;
                    }
                }
                $this->runTask($id);
            }
        } catch (TaskSchedulerException $exception) {
            if (!$this->getEventDispatcher() ||
                !$this->getEventDispatcher()->hasListeners(TaskSchedulerExceptionEvent::NAME)
            ) {
                throw $exception;
            }
            $this->getEventDispatcher()->dispatch(
                TaskSchedulerExceptionEvent::NAME,
                new TaskSchedulerExceptionEvent($exception)
            );
        }
        $this->dispatchEvent(AfterTaskSchedulerRunEvent::NAME, new AfterTaskSchedulerRunEvent($this));
    }

    /**
     * @param string $id
     * @throws Exceptions\TaskManagerException
     */
    public function runTask(string $id): void
    {
        if ($this->taskManager->checkTagetTaskManagersk($id)) {
            $this->dispatchEvent(
                BeforeTaskSchedulerHandleTaskEvent::NAME,
                new BeforeTaskSchedulerHandleTaskEvent($id, $this->taskManager->getTask($id)->getTask())
            );
            $this->storage->saveTaskLastRunTime($id, new DateTime());
            $this->taskManager->runTask($id);
            $this->dispatchEvent(
                AfterTaskSchedulerHandleTaskEvent::NAME,
                new AfterTaskSchedulerHandleTaskEvent($id, $this->taskManager->getTask($id)->getTask())
            );
        }
    }

    /**
     * @param string $id
     * @param TaskInterface $task
     * @param DateInterval|null $interval
     * @param HandlerInterface|null $handler
     * @param CheckerInterface|null $checker
     */
    public function setTask(
        string $id,
        TaskInterface $task,
        DateInterval $interval,
        HandlerInterface $handler,
        CheckerInterface $checker = null
    ): void {
        $this->taskManager->setTask($id, new TaskSchedulerContainer($task, $interval, $handler, $checker));
    }

    /**
     * @return StorageInterface
     */
    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    /**
     * @return TaskManager
     */
    public function getTaskManager(): TaskManager
    {
        return $this->taskManager;
    }

    /**
     * @param TaskManager $taskManager
     */
    public function setTaskManager(TaskManager $taskManager)
    {
        $this->taskManager = $taskManager;
    }

    /**
     * @param $id
     * @return null|TaskInterface
     */
    public function getTask(string $id): ?TaskInterface
    {
        $taskContainer = $this->taskManager->getTask($id);
        return ($taskContainer instanceof TaskContainer) ? $taskContainer->getTask() : null;
    }

    /**
     * @return TaskInterface[]
     */
    public function getTasks(): array
    {
        $tasks = [];
        foreach ($this->taskManager->getTasks() as $id => $taskContainer) {
            $tasks[$id] = $taskContainer->getTask();
        }
        return $tasks;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return TaskScheduler
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): self
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @param string $eventName
     * @param SchedulerEvent $event
     */
    protected function dispatchEvent($eventName, SchedulerEvent $event)
    {
        if ($this->getEventDispatcher()) {
            $this->getEventDispatcher()->dispatch(
                $eventName,
                $event
            );
        }
    }

    /**
     * @return EventDispatcherInterface|null
     */
    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }
}