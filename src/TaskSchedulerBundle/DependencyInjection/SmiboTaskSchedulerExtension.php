<?php
namespace Smibo\TaskSchedulerBundle\DependencyInjection;

use DateInterval;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use function var_dump;

class SmiboTaskSchedulerExtension extends Extension
{
    const EVENT_DISPATCHER_SERVICE_ID = 'event_dispatcher';

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;
        $this->config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('config.yml');

        $this->buildTaskScheduler();
        $this->loadTasks();
    }

    /**
     *
     */
    protected function buildTaskScheduler()
    {
        $this->container->setDefinition('smibo_task_scheduler',
            new Definition($this->container->getParameter('smibo_task_scheduler.class'), [
                new Definition($this->container->getParameter('smibo_task_manager.class')),
                new Reference($this->config['storage'])
            ])
        );
    }

    protected function loadTasks()
    {
        foreach ($this->config['tasks'] as $id => $task) {
            $this->container->getDefinition('smibo_task_scheduler')->addMethodCall('addTask', [
                $id,
                $this->createTaskFactoryDefinition("smibo.task_factory.{$id}", $task),
                isset($task['interval'])
                    ? new Definition(DateInterval::class, [$task['interval']])
                    : new Definition(DateInterval::class, [$this->config['default_interval']]),
                isset($task['handler'])
                    ? new Reference($task['handler'])
                    : new Reference($this->config['default_handler']),
                isset($task['checker'])
                    ? new Reference($task['checker'])
                    : (isset($this->config['default_checker'])
                        ? new Reference($this->config['default_checker'])
                        : null
                )
            ]);
        }
    }

    protected function createTaskFactoryDefinition($name, array $task)
    {
        $factoryDefinition = new Definition($this->container->getParameter('smibo_task_factory.class'), [
            $task['class'],
            isset($task['arguments']) ? $task['arguments'] : [],
        ]);
        $factoryDefinition->setPublic(false);
        $this->container->setDefinition($name, $factoryDefinition);

        $taskDefinition = new Definition($task['class']);
        $taskDefinition->setFactory([new Reference($name), 'createTask']);

        return $taskDefinition;
    }

}
