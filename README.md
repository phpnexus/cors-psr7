# CORS-PSR7

[![Latest Version](http://img.shields.io/packagist/v/phpnexus/cors-psr7.svg?style=flat-square)](https://github.com/phpnexus/cors-psr7/releases)
[![Software License](https://img.shields.io/badge/license-Apache_2.0-brightgreen.svg?style=flat-square)](LICENSE.md)

Provides CORS middleware for PSR-7 compatible frameworks.

**You may want to check these framework specific implementations for easy installation**

* [Slim 3](https://github.com/phpnexus/cors-slim3)

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install Cors-Psr7:

```bash
$ composer require phpnexus/cors-psr7
```

This package requires PHP 5.5.9 or newer.

## Usage Examples

### Slim 3

The Slim 3 framework uses PSR-7 HTTP messages and a container-interop compatible implementation of the Pimple service container.

To enable CORS support in your application, follow these steps:

#### Add the CORS service provider to your service container

*dependencies.php*

```php
$container->register(new PhpNexus\Cors\Providers\Slim3ServiceProvider);
```

#### Add the CORS PSR-7 middleware to your app

*middleware.php*

```php
$app->add(new CorsPsr7Middleware());
```

#### Add your configuration

*settings.php*

```php
$settings['cors'] = [
    'allow-methods'     => ['GET', 'POST'],
    'allow-headers'     => ['Content-Type'],
    'allow-origins'     => [],
    'allow-credentials' => false,
    'expose-headers'    => [],
    'max-age'           => 0,
];
```

## Configuration

Please see [`phpnexus\cors`](https://github.com/phpnexus/cors) documentation for configuration options.

## Roadmap

* Support for Zend Expressive
 * Support for Zend\ServiceManager and Aura.Di containers
 * Documentation
* Support for other containers NOT implementing "delegate lookup"

## Versioning

The packages adheres to the [SemVer](http://semver.org/) specification, and there will be full backward compatibility between minor versions.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

This package is released under the Apache 2.0 License. See the bundled [LICENSE](https://github.com/phpnexus/cors/blob/master/LICENSE) file for details.
