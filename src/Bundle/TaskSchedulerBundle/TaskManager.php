<?php
declare(strict_types=1);

namespace Smibo\Bundle\TaskSchedulerBundle;

use Smibo\Bundle\TaskSchedulerBundle\Exceptions\TaskManagerException;

class TaskManager
{
    /**
     * @var TaskContainer[]
     */
    protected $tasks = [];

    /**
     * @param string $id
     * @return bool
     * @throws TaskManagerException
     */
    public function checkTask(string $id): bool
    {
        if (!$this->getTask($id)) {
            throw new TaskManagerException("Task {$id} does not exist.");
        }
        return (
            $this->getTask($id)->getChecker() === null ||
            $this->getTask($id)->getChecker()->check($this->getTasks()[$id]->getTask())
        );
    }

    /**
     * @param $id
     * @return void
     * @throws TaskManagerException
     */
    public function runTask(string $id): void
    {
        if (!$this->getTask($id)) {
            throw new TaskManagerException("Task {$id} does not exist.");
        }
        $this->getTask($id)->getHandler()->handle($this->getTask($id)->getTask());
    }

    /**
     * @param $id
     * @param TaskContainer $taskContainer
     */
    public function setTask(string $id, TaskContainer $taskContainer): void
    {
        $this->tasks[$id] = $taskContainer;
    }

    /**
     * @param $id
     * @return null|TaskContainer
     */
    public function getTask(string $id): ?TaskContainer
    {
        return !empty($this->tasks[$id]) ? $this->tasks[$id] : null;
    }

    /**
     * @return TaskContainer[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}