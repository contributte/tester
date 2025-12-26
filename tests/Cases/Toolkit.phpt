<?php declare(strict_types = 1);

use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\Notes;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$bindObject = new class {

	public function call1(): void
	{
		Notes::add('CALL1');
	}

	protected function call2(): void
	{
		Notes::add('CALL2');
	}

};

Toolkit::setUp(function (): void {
	Notes::add('SETUP1');
});

Toolkit::setUp(function (): void {
	Notes::add('SETUP2');
});

Toolkit::tearDown(function (): void {
	Notes::add('DOWN1');
});

Toolkit::tearDown(function (): void {
	Notes::add('DOWN2');
});

Toolkit::bind($bindObject);

Toolkit::test(function (): void {
	$this->call1();
	Notes::add('INNER');
	$this->call2();
});

Assert::equal([
	'SETUP1',
	'SETUP2',
	'CALL1',
	'INNER',
	'CALL2',
	'DOWN1',
	'DOWN2',
], Notes::fetch());
