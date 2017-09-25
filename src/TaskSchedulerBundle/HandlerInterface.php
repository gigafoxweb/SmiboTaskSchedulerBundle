<?php
declare(strict_types=1);

namespace Smibo\TaskSchedulerBundle;

interface HandlerInterface
{
    /**
     * @param TaskInterface $task
     * @return void
     */
    function handle(TaskInterface $task) : void;
}