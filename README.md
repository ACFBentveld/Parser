# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/acfbentveld/Parser.svg?style=flat-square)](https://packagist.org/packages/acfbentveld/Parser)
[![Total Downloads](https://img.shields.io/packagist/dt/acfbentveld/Parser.svg?style=flat-square)](https://packagist.org/packages/acfbentveld/Parser)


This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require acfbentveld/Parser
```

## Basic Usage

``` php
Parser::text('Hello [who]')->values(['who' => 'world'])->parse(); // Hello world

Parser::text('Hello {who}')->values(['who' => 'world'])->tags(['{', '}'])->parse(); // Hello world

Parser::text('Hello [who]')->values(['who' => 'world'])->exclude(['who'])->parse(); // Hello [who]

Parser::text('Hello [what]')->values(['who' => 'world'])->aliases(['what' => 'who'])->parse(); // Hello world
```


## Available methods

All methods can be chained together like `text()->values()->aliases()` and can be in any order.
But you always have to start with the `text()` function.

### text
This sets the string you want to parse
``` php
    $parser = Parser::text('string')
```

### values
This sets the values to use while parsing. Must be a array
``` php
    $parser->values([]);
```

### tags
Tags are the characters around the keys you want to parse. Default `[` and `]`
``` php
    $parser->tags(['{','}']);
```

### exclude
Sets the keys which are excluded from parsing
``` php
    $parser->exclude(['key', 'key2']);
```

### aliases
Sets the aliases. Aliases can be used to map a value to a different name. 
So for example you can set the aliases to `['name' => 'username']` to map `username` to `name`
``` php
    $parser->exclude(['alias', 'value key']);
```

### parse
Parses the text and returns the parsed string
``` php
    $parser->exclude(['alias', 'value key']);
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email info@acfbentveld.nl instead of using the issue tracker.


## Credits

- [ACF Bentveld](https://github.com/ACFBentveld)
- [All Contributors](../../contributors)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
