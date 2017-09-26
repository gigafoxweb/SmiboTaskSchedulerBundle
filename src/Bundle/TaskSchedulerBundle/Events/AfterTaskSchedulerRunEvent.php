<?php
namespace Smibo\Bundle\TaskSchedulerBundle\Events;

use Smibo\Bundle\TaskSchedulerBundle\TaskScheduler;

class AfterTaskSchedulerRunEvent extends SchedulerEvent
{
    /**
     * string
     */
    const NAME = 'after.task.scheduler.run';

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
    public function getTaskScheduler(): TaskScheduler
    {
        return $this->taskScheduler;
    }
}