# DotEnv Parser

![testing workflow](https://github.com/settermjd/dotenv-parser/actions/workflows/test-code.yml/badge.svg)

This is a small package that can parse and work with [dotenv files](https://hexdocs.pm/dotenvy/dotenv-file-format.html).
It's quite new and likely needs quite a bit of work, but it's a start.

## Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require settermjd/dotenv_parser
```

## Usage

There isn't much to the package. 
You can see an example of the package's entire functionality below.

```php
$parser = new Parser('path/to/your/dotenv/file');

// Parse and retrieve the contents of the file as an associative array
$contents = $parser->getContents();

// Retrieve the value of the item with the specified key 
$parser->getItem("MY_KEY");

// Check if there is an item in the file with the specified key
$parser->hasItem("MY_KEY");

// Add a new item to the file with the specified key and value
$parser->addItem('SIZE', 2);
```