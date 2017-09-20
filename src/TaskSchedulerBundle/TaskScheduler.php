<?php
declare(strict_types=1);

namespace Smibo\TaskSchedulerBundle;


use DateInterval;
use DateTime;

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
        foreach ($this->taskManager->getTasks() as $id => $task) {
            if ($task instanceof TaskSchedulerContainer) {
                $now = new DateTime();
                if (
                    $this->storage->getTaskLastRunTime($id)->add($task->getInterval()) < $now &&
                    $this->taskManager->checkTask($id)
                ) {
                    $this->taskManager->runTask($id);
                    $this->storage->saveTaskLastRunTime($id, $now);
                }
            } else {
                if ($this->taskManager->checkTask($id)) {
                    $this->taskManager->runTask($id);
                }
            }
        }
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
}