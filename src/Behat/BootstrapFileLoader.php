<?php
namespace Athena\Behat;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BootstrapFileLoader implements Extension
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // void
    }

    /**
     * Returns the extension config key.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return 'bootstrap';
    }

    /**
     * Initializes other extensions.
     *
     * This method is called immediately after all extensions are activated but
     * before any extension `configure()` method is called. This allows extensions
     * to hook into the configuration of other extensions providing such an
     * extension point.
     *
     * @param ExtensionManager $extensionManager
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        // void
    }

    /**
     * Setups configuration for the extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->booleanNode('require_once')
                    ->info('Decides whether the as require_once call is needed.')
                    ->defaultFalse()
                ->end()
                ->scalarNode('bootstrap_path')
                    ->info('Path string for bootstrap file to be included.')
                    ->isRequired()
                    ->validate()
                        ->ifTrue(function($fileName) { return !file_exists($fileName); })
                            ->thenInvalid("Failed to find file.")
                ->end()
            ->end();
    }

    /**
     * Loads extension services into temporary container.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $bootstrapFileName = realpath($config['bootstrap_path']);

        if ($config['require_once']) {
            require_once $bootstrapFileName;
            return;
        }

        require $bootstrapFileName;
    }
}

