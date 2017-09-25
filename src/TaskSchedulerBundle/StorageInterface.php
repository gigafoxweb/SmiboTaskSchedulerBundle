<?php
declare(strict_types=1);

namespace Smibo\TaskSchedulerBundle;

interface StorageInterface
{
    /**
     * @param string $id
     * @return \DateTime
     */
    public function getTaskLastRunTime(string $id): \DateTime;

    /**
     * @param string $id
     * @param \DateTime $time
     */
    public function saveTaskLastRunTime(string $id, \DateTime $time): void;
}