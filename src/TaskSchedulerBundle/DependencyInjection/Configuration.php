<?php

namespace Smibo\TaskSchedulerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('smibo_task_scheduler');

        $this->addDefaults($rootNode);
        $this->addTasks($rootNode);

        return $treeBuilder;
    }

    protected function addDefaults(ArrayNodeDefinition $node)
    {
        $node->
            children()
                ->scalarNode('storage')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('default_interval')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('default_handler')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('default_checker')->cannotBeEmpty()->end()
            ->end()
        ;
    }

    protected function addTasks(ArrayNodeDefinition $node)
    {
        $node->
            children()
                ->arrayNode('tasks')
                    ->canBeUnset()
                    ->useAttributeAsKey('key')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('class')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('interval')->cannotBeEmpty()->end()
                                ->scalarNode('handler')->cannotBeEmpty()->end()
                                ->scalarNode('checker')->cannotBeEmpty()->end()
                                ->variableNode('arguments')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
