<?php
namespace Smibo\Bundle\TaskSchedulerBundle\Events;


use Smibo\Bundle\TaskSchedulerBundle\Exceptions\TaskSchedulerException;

class TaskSchedulerExceptionEvent extends SchedulerEvent
{
    const NAME = 'task.scheduler.exception';

    /**
     * @var TaskSchedulerException
     */
    protected $exception;

    /**
     * TaskSchedulerException constructor.
     */
    public function __construct(TaskSchedulerException $e)
    {
        $this->exception = $e;
    }

    /**
     * @return TaskSchedulerException
     */
    public function getException(): TaskSchedulerException
    {
        return $this->exception;
    }
}