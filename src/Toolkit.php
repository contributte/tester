<?php declare(strict_types = 1);

namespace Contributte\Tester;

use Closure;

class Toolkit
{

	private static ?object $bind = null;

	/** @var callable[] */
	private static array $setUp = [];

	/** @var callable[] */
	private static array $tearDown = [];

	/**
	 * @param object $object
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 */
	public static function bind($object): void
	{
		self::$bind = $object;
	}

	public static function setUp(callable $function): void
	{
		self::$setUp[] = $function;
	}

	public static function tearDown(callable $function): void
	{
		self::$tearDown[] = $function;
	}

	public static function test(callable $function): void
	{
		if (self::$bind !== null) {
			if (!$function instanceof Closure) {
				$function = Closure::fromCallable($function);
			}

			$function = Closure::bind($function, self::$bind, self::$bind);
			assert(is_callable($function));
		}

		foreach (self::$setUp as $cb) {
			$cb();
		}

		$function();

		foreach (self::$tearDown as $cb) {
			$cb();
		}
	}

}
