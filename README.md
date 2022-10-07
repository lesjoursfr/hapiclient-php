# HAPI Client

An HTTP Client implementing the [HAL specification](https://datatracker.ietf.org/doc/html/draft-kelly-json-hal-07).
This project is a fork of the original Slimpay PHP HAPI client project rewrited to fix an issue with Guzzle 7.

### Installation

```
composer install lesjoursfr/hapiclient-php
```

### Documentation

The documentation is available [here](https://lesjoursfr.github.io/hapiclient-php/)

### Development only

To install the Symphony PHP CS you have to run the following commands (assuming you have downloaded [composer.phar](https://getcomposer.org/)) :

```
php composer.phar install
vendor/bin/phpcs --config-set installed_paths vendor/escapestudios/symfony2-coding-standard
```

Then you can check the code style with the following command

```
vendor/squizlabs/php_codesniffer/bin/phpcs --standard=./phpcs.xml --no-cache --parallel=1 ./src ./tests
```

To generate the documentation you have to run (assuming you have downloaded [phpDocumentor.phar](https://www.phpdoc.org/)) :

```
php phpDocumentor.phar run -d src/ -t docs
```
