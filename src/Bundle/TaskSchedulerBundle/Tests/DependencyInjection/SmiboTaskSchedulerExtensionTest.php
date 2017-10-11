<?php

namespace Smibo\Bundle\TaskSchedulerBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Smibo\Bundle\TaskSchedulerBundle\DependencyInjection\SmiboTaskSchedulerExtension;
use Smibo\Bundle\TaskSchedulerBundle\SmiboTaskSchedulerBundle;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SmiboTaskSchedulerExtensionTest extends TestCase
{

    public function testBuildTaskScheduler()
    {
        $this->assertTrue(
            $this->createContainer('config.yml')
                ->hasDefinition(SmiboTaskSchedulerBundle::NAME)
        );
    }

    /**
     * @dataProvider taskFactoryConfigurationProvider
     */
    public function testTaskFactoryConfiguration($taskId, $taskClass, $arguments)
    {
        $container = $this->createContainer('config.yml');
        $name = "smibo.task_factory.{$taskId}";
        $this->assertTrue($container->hasDefinition(SmiboTaskSchedulerBundle::NAME));
        $this->assertTrue($container->hasDefinition($name));
        $definition = $container->getDefinition($name);
        $this->assertEquals([$taskClass, $arguments], $definition->getArguments());
    }

    public function taskFactoryConfigurationProvider()
    {
        return [
            ['testTask', 'SomeTestTask', []],
            ['testTaskWithArguments', 'SomeTestTaskWithArguments', ['asd', 123]]
        ];
    }

    /**
     * @dataProvider invalidConfigurationProvider
     */
    public function testInvalidConfiguration($file)
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->createContainer($file);
    }

    public function invalidConfigurationProvider()
    {
        return [
            ['storage_empty.yml'],
            ['default_interval_empty.yml'],
            ['default_handler_empty.yml'],
            ['default_checker_empty.yml'],
            ['task_class_empty.yml'],
        ];
    }

    private function createContainer($file, $debug = false)
    {
        $container = new ContainerBuilder(new ParameterBag(['kernel.debug' => $debug]));
        $container->registerExtension(new SmiboTaskSchedulerExtension());

        $locator = new FileLocator(__DIR__ . '/Fixtures');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load($file);

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->compile();

        return $container;
    }
}
