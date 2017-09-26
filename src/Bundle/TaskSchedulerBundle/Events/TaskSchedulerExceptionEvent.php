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
     * TaskSchedulerExceptionEvent constructor.
     * @param TaskSchedulerException $exception
     */
    public function __construct(TaskSchedulerException $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return TaskSchedulerException
     */
    public function getException(): TaskSchedulerException
    {
        return $this->exception;
    }
}