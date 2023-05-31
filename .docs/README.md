# Contributte Tester

## Installation

```bash
composer require --dev contributte/tester
```

## Usage

### Environment

Default configuration:

```php
use Contributte\Tester\Environment;

Environment::setup(__DIR__);
```

One-by-one configuration:

```php
use Contributte\Tester\Environment;

# Configure Nette\Tester
Environment::setupTester();

# Configure timezone
Environment::setupTimezone('Europe/Prague');

# Create folders (/tmp)
Environment::setupFolders(__DIR__);

# Fill global variables
Environment::setupGlobalVariables();

// Configure sessions save path
Environment::setupSessions(__DIR__);

// Allow global test() function
Environment::setupFunctions();
```

### Toolkit

`Toolkit` is class for handling anonymous tests functions.

- `Toolkit::setUp(function() { ... })` is called before test function.
- `Toolkit::tearDown(function() { ... })` is after before test function.
- `Toolkit::bind($object)` binds new context into test function, you can access `$this->` inside.
- `Toolkit::test(function() { ... })` triggers test function.

### Utils

#### Notes

Util class for capturing messages. Useful for callback testing.

```php
use Contributte\Tester\Utils\Notes;
use Tester\Assert;

$someClass->process(function() {
	Notes::add('called');
});

Assert::equal(['called'], Notes::fetch());
```

## Demo

Complete example of [`tests/bootstrap.php`](`../tests/bootstrap.php`).

```php
<?php declare(strict_types = 1);

use Contributte\Tester\Environment;

if (@!include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install dependencies using `composer update --dev`';
	exit(1);
}

// Configure environment
Environment::setup(__DIR__);
```

---------------

Thanks for testing, reporting and contributing.
