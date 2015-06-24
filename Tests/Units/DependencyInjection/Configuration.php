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
            ->isEqualTo(array('providers' => array()));
    }

    public function testGoogleProvider()
    {
        $this->array(
            $this->processConfiguration(array(
                array(
                    'providers' => array(
                        'google' => array(
                            'id' => 'google'
                        )
                    )
                )
            ))
        )
        ->isEqualTo(array(
            'providers' => array(
                'google' => array(
                    'id'           => 'google',
                    'http_adapter' => 'Rezzza\Shorty\Http\CurlAdapter',
                    'providers'    => array(),
                )
            )
        ));

        $this->array(
            $this->processConfiguration(array(
                array(
                    'providers' => array(
                        'google' => array(
                            'id'           => 'google',
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
                    'id'  => 'google',
                    'key' => 'foo',
                    'http_adapter' => 'bar',
                    'providers'    => array(),
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
                            'id' => 'bitly'
                        )
                    )
                )
            ));
        })
            ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
            ->hasMessage('“access_token“ node in “bitly“ provider is required.');

        $this->array(
            $this->processConfiguration(array(
                array(
                    'providers' => array(
                        'bitly' => array(
                            'id'           => 'bitly',
                            'access_token' => 'foo'
                        )
                    )
                )
            ))
        )
        ->isEqualTo(array(
            'providers' => array(
                'bitly' => array(
                    'id'           => 'bitly',
                    'access_token' => 'foo',
                    'http_adapter' => 'Rezzza\Shorty\Http\CurlAdapter',
                    'providers'    => array()
                )
            )
        ));

        $this->array(
            $this->processConfiguration(array(
                array(
                    'providers' => array(
                        'bitly' => array(
                            'id'           => 'bitly',
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
                    'id'           => 'bitly',
                    'access_token' => 'foo',
                    'http_adapter' => 'bar',
                    'providers'    => array()
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
                            'id' => 'chain',
                        )
                    )
                )
            ));
        })
            ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
            ->hasMessage('“providers“ node in “chain“ provider is required.');

        $self = $this;
        $this->exception(function() use ($self) {
            $self->processConfiguration(array(
                array(
                    'providers' => array(
                        'chain' => array(
                            'id'        => 'chain',
                            'providers' => array('unkown')
                        )
                    )
                )
            ));
        })
            ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
            ->hasMessage('provider “unkown“ is unknown in “chain“ provider.');


        $this->array(
            $this->processConfiguration(array(
                array(
                    'providers' => array(
                        'bitly' => array(
                            'id'        => 'bitly',
                            'access_token' => 'foo'
                        ),
                        'chain' => array(
                            'id'        => 'chain',
                            'providers' => array('bitly')
                        )
                    )
                )
            ))
        )
        ->isEqualTo(array(
            'providers' => array(
                'bitly' => array(
                    'id'           => 'bitly',
                    'access_token' => 'foo',
                    'http_adapter' => 'Rezzza\Shorty\Http\CurlAdapter',
                    'providers'    => array(),
                ),
                'chain' => array(
                    'id'        => 'chain',
                    'providers' => array('bitly'),
                    'http_adapter' => 'Rezzza\Shorty\Http\CurlAdapter',
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
                        'google' => array(
                            'id'           => 'google',
                        ),
                        'bitly' => array(
                            'id'           => 'bitly',
                            'access_token' => 'foo',
                        ),
                        'chain' => array(
                            'id'        => 'chain',
                            'providers' => array('bitly', 'google')
                        ),
                    )
                )
            ))
        )
        ->isEqualTo(array(
            'providers' => array(
                'google' => array(
                    'id'           => 'google',
                    'http_adapter' => 'Rezzza\Shorty\Http\CurlAdapter',
                    'providers'    =>  array(),
                ),
                'bitly' => array(
                    'id'           => 'bitly',
                    'access_token' => 'foo',
                    'http_adapter' => 'Rezzza\Shorty\Http\CurlAdapter',
                    'providers'    =>  array(),
                ),
                'chain' => array(
                    'id'           => 'chain',
                    'providers' => array('bitly', 'google'),
                    'http_adapter' => 'Rezzza\Shorty\Http\CurlAdapter',
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
