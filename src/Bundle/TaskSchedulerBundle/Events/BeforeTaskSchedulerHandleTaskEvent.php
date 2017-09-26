<?php
declare(strict_types=1);

namespace Smibo\Bundle\TaskSchedulerBundle\Events;

use Smibo\Bundle\TaskSchedulerBundle\TaskInterface;

class BeforeTaskSchedulerHandleTaskEvent extends SchedulerEvent
{
    /**
     * string
     */
    const NAME = 'before.task.scheduler.handle.task';

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