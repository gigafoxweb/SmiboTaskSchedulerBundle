<?php
declare(strict_types=1);

namespace Smibo\TaskSchedulerBundle;

use Smibo\TaskSchedulerBundle\Exceptions\TaskSchedulerException;

class TaskManager
{
    /**
     * @var TaskContainer[]
     */
    protected $tasks = [];

    /**
     * @param string $id
     * @return bool
     * @throws TaskSchedulerException
     */
    public function checkTask(string $id): bool
    {
        if (!isset($this->tasks[$id])) {
            throw new TaskSchedulerException("Task {$id} does not exist.");
        }
        return (
            $this->tasks[$id]->getChecker() === null ||
            $this->tasks[$id]->getChecker()->check($this->tasks[$id]->getTask())
        );
    }

    /**
     * @param $id
     * @throws TaskSchedulerException
     */
    public function runTask(string $id): void
    {
        if (!isset($this->tasks[$id])) {
            throw new TaskSchedulerException("Task {$id} does not exist.");
        }
        $this->tasks[$id]->getHandler()->handle($this->tasks[$id]->getTask());
    }

    /**
     * @param $id
     * @param TaskContainer $taskContainer
     */
    public function addTask($id, TaskContainer $taskContainer): void
    {
        $this->tasks[$id] = $taskContainer;
    }

    /**
     * @param $id
     * @return null|TaskContainer
     */
    public function getTask(string $id): ?TaskContainer
    {
        return isset($this->tasks[$id]) ? $this->tasks[$id] : null;
    }

    /**
     * @return TaskContainer[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}