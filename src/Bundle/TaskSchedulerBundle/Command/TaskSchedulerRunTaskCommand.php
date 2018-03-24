<?php

namespace Smibo\Bundle\TaskSchedulerBundle\Command;


use Smibo\Bundle\TaskSchedulerBundle\SmiboTaskSchedulerBundle;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaskSchedulerRunTaskCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName("task-scheduler:run-task")
            ->addArgument('task-id', InputArgument::REQUIRED, 'Task id.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force run without checkers and events.', 0)
            ->setDescription('Immediately runs task scheduler task.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $scheduler = $this->getContainer()->get(SmiboTaskSchedulerBundle::NAME);
        $taskId = $input->getArgument('task-id');

        if ($input->getOption('force')) {
            $scheduler->getTaskManager()->runTask($taskId);
        } else {
            $scheduler->runTask($taskId);
        }
    }
}