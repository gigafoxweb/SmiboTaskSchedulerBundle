<?php
namespace Smibo\TaskSchedulerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaskManagerCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName("task-scheduler:run")->setDescription('Runs the task manager.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('smibo_task_scheduler')->run();
    }
}