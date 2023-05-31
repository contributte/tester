<?php declare(strict_types = 1);

namespace Contributte\Tester\Utils;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

class FileSystem
{

	public static function purge(string $dir): void
	{
		if (!is_dir($dir)) {
			mkdir($dir);
		}

		/** @var SplFileInfo $entry */
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $entry) {
			if ($entry->isDir()) {
				rmdir((string) $entry->getRealPath());
			} else {
				unlink((string) $entry->getRealPath());
			}
		}
	}

	public static function mkdir(string $dir, int $mode = 0777, bool $recursive = true): void
	{
		if (is_dir($dir) === false && @mkdir($dir, $mode, $recursive) === false) {
			clearstatcache(true, $dir);
			$error = error_get_last();

			if (is_dir($dir) === false && !file_exists($dir) === false) {
				throw new RuntimeException(sprintf("Unable to create directory '%s'. " . ($error !== null ? $error['message'] : 'unknown error'), $dir));
			}
		}
	}

	public static function rmdir(string $dir): void
	{
		if (!is_dir($dir)) {
			return;
		}

		self::purge($dir);
		@rmdir($dir);
	}

}
