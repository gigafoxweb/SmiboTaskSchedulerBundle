<?php declare(strict_types=1);

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
        $taskContainer = $this->getTask($id);
        $checker = $taskContainer->getChecker();

        return $checker === null || $checker->check($id, $taskContainer->getTask());
    }

    /**
     * @param string $id
     * @return void
     * @throws TaskManagerException
     */
    public function runTask(string $id): void
    {
        $this->getTask($id)->getHandler()->handle($id, $this->getTask($id)->getTask());
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasTask(string $id): bool
    {
        return !empty($this->tasks[$id]);
    }

    /**
     * @param string $id
     * @param TaskContainer $taskContainer
     */
    public function setTask(string $id, TaskContainer $taskContainer): void
    {
        $this->tasks[$id] = $taskContainer;
    }

    /**
     * @param string $id
     * @throws TaskManagerException
     * @return null|TaskContainer
     */
    public function getTask(string $id): ?TaskContainer
    {
        if (!$this->hasTask($id)) {
            throw new TaskManagerException("Task {$id} does not exist.");
        }

        return $this->tasks[$id];
    }

    /**
     * @return TaskContainer[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}
