<?php

namespace Mutan\HelperBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MutanHelperExtension extends Extension
{
    /**
     * Loads a specific configuration.
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('mt_helper.token_generator');
        $definition->setArgument(0, $config['token_length']);
    }

    public function getAlias()
    {
        // rewrite default alias ('mutan_helper'), which is used in config .yaml files
        return 'mt_helper';
    }
}
