<?php
declare(strict_types=1);

namespace Smibo\Bundle\TaskSchedulerBundle;

interface CheckerInterface
{
    /**
     * @param TaskInterface $task
     * @return bool
     */
    function check(TaskInterface $task): bool;
}