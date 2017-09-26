<?php
declare(strict_types=1);

namespace Smibo\TaskSchedulerBundle;

interface CheckerInterface
{
    /**
     * @param TaskInterface $task
     * @return bool
     */
    function check(TaskInterface $task): bool;
}