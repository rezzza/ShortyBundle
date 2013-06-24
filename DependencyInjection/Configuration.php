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
                    ->arrayNode('providers')
                        ->children()
                            ->arrayNode('google')
                            ->info('google shortener configuration')
                                ->children()
                                    ->scalarNode('key')->defaultValue(null)->end()
                                    ->scalarNode('http_adapter')->defaultValue('Rezzza\Shorty\Http\GuzzleAdapter')->end()
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
