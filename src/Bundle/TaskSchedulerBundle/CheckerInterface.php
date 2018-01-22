<?php
declare(strict_types=1);

namespace Smibo\Bundle\TaskSchedulerBundle;

interface CheckerInterface
{
    /**
     * @param string $id
     * @param TaskInterface $task
     * @return bool
     */
    function check(string $id, TaskInterface $task): bool;
}