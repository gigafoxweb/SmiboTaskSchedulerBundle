<?php
namespace Smibo\TaskSchedulerBundle;


class TaskContainer
{
    /**
     * @var TaskInterface
     */
    protected $task;

    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * @var CheckerInterface
     */
    protected $checker;

    /**
     * TaskContainer constructor.
     * @param TaskInterface $task
     * @param HandlerInterface $handler
     * @param CheckerInterface|null $checker
     */
    public function __construct(TaskInterface $task, HandlerInterface $handler, CheckerInterface $checker = null)
    {
        $this->task = $task;
        $this->handler = $handler;
        $this->checker = $checker;
    }

    /**
     * @return TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @return HandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @return CheckerInterface
     */
    public function getChecker()
    {
        return $this->checker;
    }
}