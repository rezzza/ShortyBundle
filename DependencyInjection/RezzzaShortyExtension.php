<?php

namespace Rezzza\ShortyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;

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

        foreach ($config['providers'] as $provider => $data) {
            $class              = $container->getParameter(sprintf('rezzza.shorty.%s.class', $provider));

            switch ($provider) {
                case 'google':
                    $providerDefinition = new Definition($class, array($data['key']));
                    break;
                default:
                    throw new \InvalidArgumentException('Unsupported provider');
                    break;
            }

            $providerDefinition->addMethodCall('setHttpAdapter', array(new Definition($data['http_adapter'])));

            $container->setDefinition(sprintf('rezzza.shorty.%s', $provider), $providerDefinition);
        }
    }
}
