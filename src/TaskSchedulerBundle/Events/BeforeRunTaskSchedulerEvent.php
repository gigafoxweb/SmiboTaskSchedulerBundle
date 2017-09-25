<?php
namespace Smibo\TaskSchedulerBundle\Events;

use Smibo\TaskSchedulerBundle\TaskScheduler;

class BeforeRunTaskSchedulerEvent extends SchedulerEvent
{
    /**
     * string
     */
    const NAME = 'before.run.task.scheduler';

    /**
     * @var TaskScheduler
     */
    protected $taskScheduler;

    /**
     * BeforeRunTaskSchedulerEvent constructor.
     */
    public function __construct(TaskScheduler $taskScheduler)
    {
        $this->taskScheduler = $taskScheduler;
    }

    /**
     * @return TaskScheduler
     */
    public function getTaskScheduler()
    {
        return $this->taskScheduler;
    }
}