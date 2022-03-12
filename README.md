<p align="center">
    <a href="https://github.com/php-ffi-headers">
        <img src="https://avatars.githubusercontent.com/u/101121010?s=256" width="128" />
    </a>
</p>

<p align="center">
    <a href="https://github.com/php-ffi-headers/bass-mix-headers/actions"><img src="https://github.com/php-ffi-headers/bass-mix-headers/workflows/build/badge.svg"></a>
    <a href="https://packagist.org/packages/ffi-headers/bass-mix-headers"><img src="https://img.shields.io/badge/PHP-8.1.0-ff0140.svg"></a>
    <a href="https://packagist.org/packages/ffi-headers/bass-mix-headers"><img src="https://img.shields.io/badge/BASS%20Mix-2.4-cc3c20.svg"></a>
    <a href="https://packagist.org/packages/ffi-headers/bass-mix-headers"><img src="https://poser.pugx.org/ffi-headers/bass-mix-headers/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/ffi-headers/bass-mix-headers"><img src="https://poser.pugx.org/ffi-headers/bass-mix-headers/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://packagist.org/packages/ffi-headers/bass-mix-headers"><img src="https://poser.pugx.org/ffi-headers/bass-mix-headers/downloads" alt="Total Downloads"></a>
    <a href="https://raw.githubusercontent.com/php-ffi-headers/bass-mix-headers/master/LICENSE.md"><img src="https://poser.pugx.org/ffi-headers/bass-mix-headers/license" alt="License MIT"></a>
</p>

# Bass Mix Headers

This is a C headers of the [Bass Mix Audio Extension](http://www.un4seen.com/) adopted for PHP.

## Requirements

- PHP >= 8.1

## Installation

Library is available as composer repository and can be installed using the
following command in a root of your project.

```sh
$ composer require ffi-headers/bass-mix-headers
```

## Usage

```php
use FFI\Headers\BassMix;

$headers = BassMix::create(
    BassMix\Version::V2_4,
);

echo $headers;
```

> Please note that the use of header files is not the latest version:
> - Takes time to download and install (This will be done in the background
    >   during initialization).
> - May not be compatible with the PHP headers library.

