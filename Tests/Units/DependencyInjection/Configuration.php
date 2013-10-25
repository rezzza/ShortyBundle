<?php

namespace Rezzza\ShortyBundle\Tests\Units\DependencyInjection;

require_once __DIR__."/../../../vendor/autoload.php";

use atoum\AtoumBundle\Test\Units\Test;
use Rezzza\ShortyBundle\DependencyInjection\Configuration as ConfigurationTested;
use Symfony\Component\Config\Definition\Processor;

/**
 * Configuration
 *
 * @uses Test
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Configuration extends Test
{
    public function testNoConfiguration()
    {
        $this->array($this->processConfiguration(array(array())))
            ->isEqualTo(array());
    }

    public function testUnknownProvider()
    {
        $self = $this;
        $this->exception(function() use ($self) {
            $self->processConfiguration(array(
                array(
                    'providers' => array(
                        'unknown' => array(
                        )
                    )
                )
            ));
        })
            ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
            ->hasMessage('Unrecognized options "unknown" under "rezzza_shorty.providers"')
            ;
    }

    public function testGoogleProvider()
    {
        $this->array(
            $this->processConfiguration(array(
                array(
                    'providers' => array(
                        'google' => array(
                        )
                    )
                )
            ))
        )
        ->isEqualTo(array(
            'providers' => array(
                'google' => array(
                    'http_adapter' => 'Rezzza\Shorty\Http\CurlAdapter',
                )
            )
        ));

        $this->array(
            $this->processConfiguration(array(
                array(
                    'providers' => array(
                        'google' => array(
                            'key'          => 'foo',
                            'http_adapter' => 'bar'
                        )
                    )
                )
            ))
        )
        ->isEqualTo(array(
            'providers' => array(
                'google' => array(
                    'key' => 'foo',
                    'http_adapter' => 'bar',
                )
            )
        ));
    }

    public function testBitlyProvider()
    {
        $self = $this;
        $this->exception(function() use ($self) {
            $self->processConfiguration(array(
                array(
                    'providers' => array(
                        'bitly' => array(
                        )
                    )
                )
            ));
        })
            ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
            ->hasMessage('The child node "access_token" at path "rezzza_shorty.providers.bitly" must be configured.');


        $this->array(
            $this->processConfiguration(array(
                array(
                    'providers' => array(
                        'bitly' => array(
                            'access_token' => 'foo'
                        )
                    )
                )
            ))
        )
        ->isEqualTo(array(
            'providers' => array(
                'bitly' => array(
                    'access_token' => 'foo',
                    'http_adapter' => 'Rezzza\Shorty\Http\CurlAdapter',
                )
            )
        ));

        $this->array(
            $this->processConfiguration(array(
                array(
                    'providers' => array(
                        'bitly' => array(
                            'access_token' => 'foo',
                            'http_adapter' => 'bar'
                        )
                    )
                )
            ))
        )
        ->isEqualTo(array(
            'providers' => array(
                'bitly' => array(
                    'access_token' => 'foo',
                    'http_adapter' => 'bar',
                )
            )
        ));
    }

    public function testChainProvider()
    {
        $self = $this;
        $this->exception(function() use ($self) {
            $self->processConfiguration(array(
                array(
                    'providers' => array(
                        'chain' => array(
                        )
                    )
                )
            ));
        })
            ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
            ->hasMessage('The child node "providers" at path "rezzza_shorty.providers.chain" must be configured.');

        $self = $this;
        $this->exception(function() use ($self) {
            $self->processConfiguration(array(
                array(
                    'providers' => array(
                        'chain' => array(
                            'providers' => array('unkown')
                        )
                    )
                )
            ));
        })
            ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
            ->hasMessage('Invalid configuration for path "rezzza_shorty.providers": RezzzaShorty - A provider defined in chain is not exists.');


        $this->array(
            $this->processConfiguration(array(
                array(
                    'providers' => array(
                        'bitly' => array(
                            'access_token' => 'foo'
                        ),
                        'chain' => array(
                            'providers' => array('bitly')
                        )
                    )
                )
            ))
        )
        ->isEqualTo(array(
            'providers' => array(
                'bitly' => array(
                    'access_token' => 'foo',
                    'http_adapter' => 'Rezzza\Shorty\Http\CurlAdapter',
                ),
                'chain' => array(
                    'providers' => array('bitly')
                ),
            )
        ));
    }


    public function testAll()
    {
        $this->array(
            $this->processConfiguration(array(
                array(
                    'providers' => array(
                        'google' => array(),
                        'bitly' => array(
                            'access_token' => 'foo',
                        ),
                        'chain' => array(
                            'providers' => array('bitly', 'google')
                        ),
                    )
                )
            ))
        )
        ->isEqualTo(array(
            'providers' => array(
                'google' => array(
                    'http_adapter' => 'Rezzza\Shorty\Http\CurlAdapter',
                ),
                'bitly' => array(
                    'access_token' => 'foo',
                    'http_adapter' => 'Rezzza\Shorty\Http\CurlAdapter',
                ),
                'chain' => array(
                    'providers' => array('bitly', 'google')
                ),
            )
        ));
    }

    public function processConfiguration($config)
    {
        $processor     = new Processor();
        $configuration = new ConfigurationTested();

        return $processor->processConfiguration($configuration, $config);
    }
}
