<?php
namespace Smibo\Bundle\TaskSchedulerBundle\Command;

use Smibo\Bundle\TaskSchedulerBundle\Events\TaskSchedulerExceptionEvent;
use Smibo\Bundle\TaskSchedulerBundle\Exceptions\TaskSchedulerException;
use Smibo\Bundle\TaskSchedulerBundle\SmiboTaskSchedulerBundle;
use Smibo\Bundle\TaskSchedulerBundle\TaskScheduler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaskSchedulerCommand extends ContainerAwareCommand
{
    /**
     *
     */
    public function configure()
    {
        $this->setName("task-scheduler:run")->setDescription('Runs task scheduler.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $taskScheduler TaskScheduler */
        $taskScheduler = $this->getContainer()->get(SmiboTaskSchedulerBundle::NAME);
        try {
            $taskScheduler->run();
        } catch (TaskSchedulerException $e) {
            if (!$taskScheduler->getEventDispatcher() ||
                !$taskScheduler->getEventDispatcher()->hasListeners(TaskSchedulerExceptionEvent::NAME)
            ) {
                throw $e;
            }
            $taskScheduler->getEventDispatcher()->dispatch(
                TaskSchedulerExceptionEvent::NAME,
                new TaskSchedulerExceptionEvent($e)
            );
        }
    }
}