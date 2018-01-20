<?php
declare(strict_types=1);

namespace Smibo\Bundle\TaskSchedulerBundle;

use DateInterval;
use DateTime;

interface StorageInterface
{
    /**
     * @param string $id
     * @param DateInterval $interval
     * @return DateTime
     */
    public function getTaskLastRunTime(string $id, DateInterval $interval): DateTime;

    /**
     * @param string $id
     * @param DateTime $time
     */
    public function saveTaskLastRunTime(string $id, DateTime $time): void;
}