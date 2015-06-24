<?php

namespace Rezzza\ShortyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

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
                        ->useAttributeAsKey('providerName')
                        ->validate()
                            ->always(function($providers) {
                                foreach ($providers as $name => $provider) {
                                    switch ($provider['id']) {
                                        case 'bitly':
                                            if (false === isset($provider['access_token'])) {
                                                throw new InvalidConfigurationException('“access_token“ node in “bitly“ provider is required.');
                                            }
                                            break;
                                        case 'chain':
                                            if (empty($provider['providers'])) {
                                                throw new InvalidConfigurationException('“providers“ node in “chain“ provider is required.');
                                            }

                                            foreach ($provider['providers'] as $providerName) {
                                                if (false === array_key_exists($providerName, $providers)) {
                                                    throw new InvalidConfigurationException(sprintf('provider “%s“ is unknown in “chain“ provider.', $providerName));
                                                }

                                                if ($providerName === $name) {
                                                    throw new InvalidConfigurationException(sprintf('Provider “%s“ is a circular reference, remove it from “%s“ provider.', $providerName, $providerName));
                                                }
                                            }
                                            break;
                                    }
                                }

                                return $providers;
                            })
                        ->end()
                        ->prototype('array')
                            ->children()
                                ->scalarNode('id')->isRequired()->end()
                                ->scalarNode('http_adapter')->defaultValue('Rezzza\Shorty\Http\CurlAdapter')->end()
                                ->scalarNode('key')->end()
                                ->scalarNode('access_token')->end()
                                ->arrayNode('providers')
                                    ->prototype('scalar')->end()
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
