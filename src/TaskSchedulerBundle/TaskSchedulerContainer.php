<?php
declare(strict_types=1);

namespace Smibo\TaskSchedulerBundle;

use DateInterval;

class TaskSchedulerContainer extends TaskContainer
{
    /**
     * @var DateInterval
     */
    protected $interval;

    /**
     * TaskSchedulerContainer constructor.
     * @param TaskInterface $task
     * @param DateInterval $interval
     * @param HandlerInterface $handler
     * @param CheckerInterface $checker
     */
    public function __construct(
        TaskInterface $task,
        DateInterval $interval,
        HandlerInterface $handler,
        CheckerInterface $checker = null
    ) {
        parent::__construct($task, $handler, $checker);
        $this->interval = $interval;
    }

    /**
     * @return DateInterval
     */
    public function getInterval(): DateInterval
    {
        return $this->interval;
    }
}