<?php declare(strict_types = 1);

namespace Contributte\Tester\Utils;

class Httpkit
{

	public static function wrap(callable $callback): void
	{
		ob_start();
		$callback();
		ob_end_clean();
	}

}
