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

        $providers = $config['providers'];

        if (isset($providers['google'])) {
            $definition = new Definition(
                $container->getParameter('rezzza.shorty.google.class'),
                array($providers['google']['key'])
            );
            $definition->addMethodCall('setHttpAdapter', array(new Definition($providers['google']['http_adapter'])));
            $container->setDefinition('rezzza.shorty.google', $definition);
        }

        if (isset($providers['bitly'])) {
            $definition = new Definition(
                $container->getParameter('rezzza.shorty.bitly.class'),
                array($providers['bitly']['access_token'])
            );
            $definition->addMethodCall('setHttpAdapter', array(new Definition($providers['bitly']['http_adapter'])));
            $container->setDefinition('rezzza.shorty.bitly', $definition);
        }

        if (isset($providers['chain'])) {
            $definition = new Definition($container->getParameter('rezzza.shorty.chain.class'));
            foreach ($providers['chain']['providers'] as $provider) {
                $definition->addMethodCall('addProvider', array(new Reference(sprintf('rezzza.shorty.%s', $provider))));
            }
            $container->setDefinition('rezzza.shorty.chain', $definition);
        }

        if (isset($config['default_provider'])) {
            $container->setAlias('rezzza.shorty', sprintf('rezzza.shorty.%s', $config['default_provider']));
        }
    }
}
