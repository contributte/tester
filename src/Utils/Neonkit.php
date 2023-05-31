<?php declare(strict_types = 1);

namespace Contributte\Tester\Utils;

use Nette\DI\Config\Adapters\NeonAdapter;
use Nette\Neon\Neon;

class Neonkit
{

	/**
	 * @return mixed[]
	 */
	public static function load(string $str): array
	{
		return (new NeonAdapter())->process((array) Neon::decode($str));
	}

	/**
	 * @return mixed[]
	 */
	public static function loadFile(string $file): array
	{
		return (new NeonAdapter())->process((array) Neon::decodeFile($file));
	}

}
