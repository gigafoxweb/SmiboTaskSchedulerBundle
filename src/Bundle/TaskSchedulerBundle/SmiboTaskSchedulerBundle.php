<?php
namespace Smibo\Bundle\TaskSchedulerBundle;

use Smibo\Bundle\TaskSchedulerBundle\DependencyInjection\Compiler\InjectEventDispatcherPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SmiboTaskSchedulerBundle extends Bundle {

    /**
     * string
     */
    const NAME = 'smibo_task_scheduler';

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new InjectEventDispatcherPass());
    }
}