# Contributte Tester

## Installation

```bash
composer require --dev contributte/tester
```

## Usage

### Environment

```php
use Contributte\Tester\Environment;

# Configure Nette\Tester
Environment::setupTester();

# Configure timezone (Europe/Prague by default)
Environment::setupTimezone();

# Configure many constants
Environment::setupVariables();

# Fill global variables
Environment::setupGlobalVariables();

# Register robot loader
Environment::setupRobotLoader();
Environment::setupRobotLoader(function($loader){});
```

### TestCases

There are many predefined test cases.

- `BaseTestCase`
- `BaseMockeryTestCase` + `TMockeryTestCase`
- `BaseMockistaTestCase` + `TMockistaTestCase`
- `BaseContainerTestCase` + `TContainerTestCase`

### Toolkit

`Toolkit` is class for handling anonymous tests functions.

- `Toolkit::setUp(function() { ... })` is called before test function.
- `Toolkit::tearDown(function() { ... })` is after before test function.
- `Toolkit::bind($object)` binds new context into test function, you can access `$this->` inside.
- `Toolkit::test(function() { ... })` triggers test function.

### Notes

Little helper to your tests.

```php
use Contributte\Tester\Notes;

Notes::add('My note');

# ['My note']
$notes = Notes::fetch();

Notes::clear();
```

---------------

Thanks for testing, reporting and contributing.
