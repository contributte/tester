<?php declare(strict_types = 1);

namespace Contributte\Tester\Utils;

class Notes
{

	/** @var string[] */
	public static array $notes = [];

	public static function add(string $message): void
	{
		self::$notes[] = $message;
	}

	public static function clear(): void
	{
		self::$notes = [];
	}

	/**
	 * @return string[]
	 */
	public static function fetch(): array
	{
		$res = self::$notes;
		self::$notes = [];

		return $res;
	}

}
