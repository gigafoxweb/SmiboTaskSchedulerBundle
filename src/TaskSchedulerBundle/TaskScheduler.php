<?php
declare(strict_types=1);

namespace Smibo\TaskSchedulerBundle;

use DateInterval;
use DateTime;
use Smibo\TaskSchedulerBundle\Events\AfterHandleTaskEvent;
use Smibo\TaskSchedulerBundle\Events\BeforeHandleTaskEvent;
use Smibo\TaskSchedulerBundle\Events\BeforeRunTaskSchedulerEvent;
use Smibo\TaskSchedulerBundle\Events\SchedulerEvent;
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
     *
     */
    public function run(): void
    {
        $this->dispatchEvent(BeforeRunTaskSchedulerEvent::NAME, new BeforeRunTaskSchedulerEvent($this));
        foreach ($this->taskManager->getTasks() as $id => $task) {
            if ($task instanceof TaskSchedulerContainer) {
                $now = new DateTime();
                if (
                    $this->storage->getTaskLastRunTime($id)->add($task->getInterval()) < $now &&
                    $this->taskManager->checkTask($id)
                ) {
                    $this->runTask($id, $task);
                    $this->storage->saveTaskLastRunTime($id, $now);
                }
            } else {
                if ($this->taskManager->checkTask($id)) {
                    $this->runTask($id, $task);
                }
            }
        }
    }

    /**
     * @param string $id
     * @param TaskContainer $task
     */
    protected function runTask(string $id, TaskContainer $task)
    {
        $this->dispatchEvent(BeforeHandleTaskEvent::NAME, new BeforeHandleTaskEvent($id, $task->getTask()));
        $this->taskManager->runTask($id);
        $this->dispatchEvent(AfterHandleTaskEvent::NAME, new AfterHandleTaskEvent($id, $task->getTask()));
    }

    /**
     * @param string $id
     * @param TaskInterface $task
     * @param DateInterval|null $interval
     * @param HandlerInterface|null $handler
     * @param CheckerInterface|null $checker
     */
    public function addTask(
        string $id,
        TaskInterface $task,
        DateInterval $interval,
        HandlerInterface $handler,
        CheckerInterface $checker = null
    ): void {
        $this->taskManager->addTask($id, new TaskSchedulerContainer($task, $interval, $handler, $checker));
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
    public function getTask(string $id): TaskInterface
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
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @param string $eventName
     * @param SchedulerEvent  $event
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
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }
}