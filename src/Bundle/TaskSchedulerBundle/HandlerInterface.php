<?php
declare(strict_types=1);

namespace Smibo\Bundle\TaskSchedulerBundle;

interface HandlerInterface
{
    /**
     * @param string $id
     * @param TaskInterface $task
     * @return void
     */
    function handle(string $id, TaskInterface $task): void;
}