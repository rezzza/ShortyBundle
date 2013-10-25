<?php

namespace Rezzza\ShortyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder
            ->root('rezzza_shorty')
                ->children()
                    ->scalarNode('default_provider')->end()
                    ->arrayNode('providers')
                        ->validate()
                            ->ifTrue(function($v) {
                                if (!isset($v['chain'])) {
                                    return false;
                                }

                                foreach ($v['chain']['providers'] as $provider) {
                                    if (!isset($v[$provider])) {
                                        return true;
                                    }
                                }
                            })
                            ->thenInvalid('RezzzaShorty - A provider defined in chain is not exists.')
                        ->end()
                        ->children()
                            ->arrayNode('google')
                                ->children()
                                    ->scalarNode('key')->end()
                                    ->scalarNode('http_adapter')->defaultValue('Rezzza\Shorty\Http\CurlAdapter')->end()
                                ->end()
                            ->end()
                            ->arrayNode('bitly')
                                ->children()
                                    ->scalarNode('access_token')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('http_adapter')->defaultValue('Rezzza\Shorty\Http\CurlAdapter')->end()
                                ->end()
                            ->end()
                            ->arrayNode('chain')
                                ->children()
                                    ->arrayNode('providers')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
