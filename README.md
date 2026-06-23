![](https://heatbadger.now.sh/github/readme/contributte/tester/)

<p align=center>
  <a href="https://github.com/contributte/tester/actions"><img src="https://badgen.net/github/checks/contributte/tester/master?cache=300"></a>
  <a href="https://codecov.io/gh/contributte/tester"><img src="https://badgen.net/codecov/c/github/contributte/tester"></a>
  <a href="https://packagist.org/packages/contributte/tester"><img src="https://badgen.net/packagist/dm/contributte/tester"></a>
  <a href="https://packagist.org/packages/contributte/tester"><img src="https://badgen.net/packagist/v/contributte/tester"></a>
</p>
<p align=center>
  <a href="https://packagist.org/packages/contributte/tester"><img src="https://badgen.net/packagist/php/contributte/tester"></a>
  <a href="https://github.com/contributte/tester"><img src="https://badgen.net/github/license/contributte/tester"></a>
  <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
  <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
  <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

Helpers and utilities for bootstrapping and writing tests with Nette Tester.

## Versions

| State  | Version | Branch   | Nette | PHP     |
|--------|---------|----------|-------|---------|
| dev    | `^0.5`  | `master` | 3.2+  | `>=8.2` |
| stable | `^0.4`  | `master` | 3.2+  | `>=8.2` |

## Installation

To install latest version of `contributte/tester` use [Composer](https://getcomposer.org).

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

#### ContainerPatcher

Util class for replacing existing services in compiled Nette DI containers.

```php
use Contributte\Tester\Utils\ContainerPatcher;
use Nette\DI\Container;

ContainerPatcher::of($container)
	// replace one service by name
	->service('http.client', fn (Container $container): object => new FakeHttpClient())
	// replace all services by type (closure can receive service name)
	->type(App\Contracts\Clock::class, fn (Container $container, string $name): object => new FrozenClock())
	// replace all services tagged by a custom tag
	->tag('app.transport', fn (): object => new InMemoryTransport())
	// patch by pre-built service instance
	->serviceInstance('http.client', new FakeHttpClient());
```

Available methods:

- `ContainerPatcher::of($container)` creates patcher for given container.
- `->service('name', $factory)` patches service by service name.
- `->type(SomeClass::class, $factory)` patches all services matching type.
- `->tag('tag.name', $factory)` patches all services with given tag.
- `->serviceInstance()`, `->typeInstance()`, `->tagInstance()` patch by pre-built object instance.

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

Complete example of [`tests/bootstrap.php`](tests/bootstrap.php).

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

## Development

See [how to contribute](https://contributte.org) to this package. This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

<a href="https://github.com/vody105">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/22433893?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for using this package.
