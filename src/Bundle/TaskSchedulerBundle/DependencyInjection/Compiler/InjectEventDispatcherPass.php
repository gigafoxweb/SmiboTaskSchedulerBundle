<?php
namespace Smibo\Bundle\TaskSchedulerBundle\DependencyInjection\Compiler;


use Smibo\Bundle\TaskSchedulerBundle\SmiboTaskSchedulerBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

class InjectEventDispatcherPass implements CompilerPassInterface
{
    const EVENT_DISPATCHER_SERVICE_ID = 'event_dispatcher';

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::EVENT_DISPATCHER_SERVICE_ID)) {
            return;
        }
        $definition = $container->getDefinition(SmiboTaskSchedulerBundle::NAME);
        $definition->addMethodCall(
            'setEventDispatcher',
            [
                new Reference(self::EVENT_DISPATCHER_SERVICE_ID, ContainerInterface::IGNORE_ON_INVALID_REFERENCE)
            ]
        );
    }
}