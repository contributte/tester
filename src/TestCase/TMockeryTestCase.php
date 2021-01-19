<?php declare(strict_types = 1);

namespace Contributte\Tester\TestCase;

use Mockery;
use Mockery\Container;

trait TMockeryTestCase
{

	/** @var Container */
	protected $mockery;

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->mockery = Mockery::getContainer();
	}

	/**
	 * This method is called after a test is executed.
	 */
	protected function tearDown(): void
	{
		parent::tearDown();

		$this->mockery->mockery_verify();
	}

}
