<?php
declare(strict_types=1);

namespace Smibo\TaskSchedulerBundle\Events;

use Smibo\TaskSchedulerBundle\TaskInterface;

class BeforeHandleTaskEvent extends SchedulerEvent
{
    /**
     * string
     */
    const NAME = 'before.handle.task';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var TaskInterface
     */
    protected $task;

    /**
     * BeforeHandleTaskEvent constructor.
     * @param TaskInterface $task
     */
    public function __construct(string $id, TaskInterface $task)
    {
        $this->id = $id;
        $this->task = $task;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return TaskInterface
     */
    public function getTask(): TaskInterface
    {
        return $this->task;
    }
}