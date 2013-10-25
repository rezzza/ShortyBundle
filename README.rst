ShortyBundle
============

.. image:: https://poser.pugx.org/rezzza/shorty-bundle/version.png
   :target: https://packagist.org/packages/rezzza/shorty-bundle

.. image:: https://travis-ci.org/rezzza/ShortyBundle.png?branch=master
   :target: http://travis-ci.org/rezzza/ShortyBundle

Underwear for your long urls in Symfony.

Integration of `Shorty <https://github.com/rezzza/Shorty>`_ library.

Installation
------------
Use `Composer <https://github.com/composer/composer/>`_ to install: ``rezzza/shorty-bundle``.

In your ``composer.json`` you should have:

.. code-block:: yaml

    {
        "require": {
            "rezzza/shorty-bundle": "1.1.*"
        }
    }

Then update your ``AppKernel.php`` to register the bundle with:

.. code-block:: php

    new Rezzza\ShortyBundle\RezzzaShortyBundle()

Configuration
-------------

.. code-block:: yaml

    rezzza_shorty:
        default_provider: google
        providers:
            google:
                key: ~
                http_adapter: ~ # default is Rezzza\Shorty\Http\CurlAdapter
            bitly:
                access_token: ~ #required
                http_adapter: ~ # default is Rezzza\Shorty\Http\CurlAdapter
            chain:
                providers: [google, bitly]

Basic usage
-----------

.. code-block:: php

    $shorty = $this->container->get('rezzza.shorty.google');
    // or
    $shorty = $this->container->get('rezzza.shorty'); // will use default_provider.
    try {
        $short  = $shorty->shorten('http://www.verylastroom.net');
        $long   = $shorty->expand($long);
    } catch (\Rezzza\Shorty\Exception\Exception $e) {
        // oops ...
    }

Exceptions
----------

`Exceptions <https://github.com/rezzza/Shorty/tree/master/src/Rezzza/Shorty/Exception>`_ directory.
