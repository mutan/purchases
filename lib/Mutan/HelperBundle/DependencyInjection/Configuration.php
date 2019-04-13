<?php

namespace Mutan\HelperBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('mt_helper');
        $treeBuilder->getRootNode()
            ->children()
                ->integerNode('token_length')->defaultValue(16)->info('Length of the token string')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
