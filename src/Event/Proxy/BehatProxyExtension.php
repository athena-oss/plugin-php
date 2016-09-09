<?php
namespace Athena\Event\Proxy;

use Athena\Event\Dispatcher\DispatcherLocator;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class BehatProxyExtension implements Extension
{
    public function process(ContainerBuilder $container)
    {
        // void
    }

    public function getConfigKey()
    {
        return 'athena_proxy';
    }

    public function initialize(ExtensionManager $extensionManager)
    {
        // void
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        // void
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $definition = new Definition(BehatProxy::class);
        $definition->addTag('event_dispatcher.subscriber');
        $definition->addArgument((new DispatcherLocator())->locate());

        $container->setDefinition('athena.listener', $definition);
    }
}

