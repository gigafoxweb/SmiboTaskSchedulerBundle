<?php

namespace Smibo\Bundle\TaskSchedulerBundle\Tests;


use PHPUnit\Framework\TestCase;
use Smibo\Bundle\TaskSchedulerBundle\CheckerInterface;
use Smibo\Bundle\TaskSchedulerBundle\Exceptions\TaskManagerException;
use Smibo\Bundle\TaskSchedulerBundle\HandlerInterface;
use Smibo\Bundle\TaskSchedulerBundle\TaskContainer;
use Smibo\Bundle\TaskSchedulerBundle\TaskInterface;
use Smibo\Bundle\TaskSchedulerBundle\TaskManager;

class SmiboTaskManagerTest extends TestCase
{
    public function testCheckTaskIsValid()
    {
        $taskManager = new TaskManager();
        $taskManager->setTask('testId', new TaskContainer(
            new class implements TaskInterface {},
            new class implements HandlerInterface { public function handle(TaskInterface $task): void {} }
        ));

        $this->assertTrue($taskManager->checkTask('testId'));
    }

    public function testCheckTaskIsNotValid()
    {
        $taskManager = new TaskManager();
        $taskManager->setTask('testId', new TaskContainer(
            new class implements TaskInterface {},
            new class implements HandlerInterface { public function handle(TaskInterface $task): void {} },
            new class implements CheckerInterface { public function check(TaskInterface $task): bool { return false; } }
        ));

        $this->assertFalse($taskManager->checkTask('testId'));
    }

    public function testCheckTaskDoesNotExist()
    {
        $this->expectException(TaskManagerException::class);

        $taskManager = new TaskManager();
        $taskManager->checkTask('testId');
    }

    public function testRunTaskDoesNotExist()
    {
        $this->expectException(TaskManagerException::class);

        $taskManager = new TaskManager();
        $taskManager->runTask('testId');
    }
}
