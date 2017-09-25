<?php
namespace Smibo\TaskSchedulerBundle\Command;

use Smibo\TaskSchedulerBundle\SmiboTaskSchedulerBundle;
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
        $this->getContainer()->get(SmiboTaskSchedulerBundle::NAME)->run();
    }
}