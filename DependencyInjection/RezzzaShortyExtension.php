<?php

namespace Rezzza\ShortyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class RezzzaShortyExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('shorty.xml');

        foreach ($config['providers'] as $providerName => $providerConfiguration) {
            switch ($providerConfiguration['id']) {
                case 'google':
                    $definition = new Definition($container->getParameter('rezzza.shorty.google.class'), array($providerConfiguration['key']));
                    $definition->addMethodCall('setHttpAdapter', array(new Definition($providerConfiguration['http_adapter'])));
                    $container->setDefinition($this->getShortyProviderName($providerName), $definition);
                    break;
                case 'bitly':
                    $definition = new Definition($container->getParameter('rezzza.shorty.bitly.class'), array($providerConfiguration['access_token']));
                    $definition->addMethodCall('setHttpAdapter', array(new Definition($providerConfiguration['http_adapter'])));
                    $container->setDefinition($this->getShortyProviderName($providerName), $definition);
                    break;
                case 'chain':
                    $definition = new Definition($container->getParameter('rezzza.shorty.chain.class'));
                    foreach ($providerConfiguration['providers'] as $provider) {
                        $definition->addMethodCall('addProvider', array(new Reference($this->getShortyProviderName($provider))));
                    }
                    $container->setDefinition($this->getShortyProviderName($providerName), $definition);
                    break;
                default:
                    $container->setAlias($this->getShortyProviderName($providerName), $providerConfiguration['id']);
                    break;
            }
        }

        if (isset($config['default_provider'])) {
            $container->setAlias('rezzza.shorty', $this->getShortyProviderName($config['default_provider']));
        }
    }

    private function getShortyProviderName($name)
    {
        return sprintf('rezzza.shorty.%s', $name);
    }
}
