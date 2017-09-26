<?php
declare(strict_types=1);

namespace Smibo\Bundle\TaskSchedulerBundle;

use Smibo\Bundle\TaskSchedulerBundle\Exceptions\TaskCreationException;

class TaskFactory
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * TaskFactory constructor.
     * @param string $class
     * @param array $arguments
     */
    public function __construct(string $class, array $arguments = [])
    {
        $this->class = $class;
        $this->arguments = $arguments;
    }

    /**
     * @return TaskInterface
     * @throws TaskCreationException
     */
    public function createTask(): TaskInterface
    {
        $reflection = new \ReflectionClass($this->class);
        if (!$reflection->implementsInterface(TaskInterface::class)) {
            throw new TaskCreationException('Task must implement ' . TaskInterface::class . ' interface');
        }
        /* @var $task TaskInterface */
        $task = $reflection->newInstanceArgs($this->arguments);
        return $task;
    }
}