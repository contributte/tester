<?php declare(strict_types = 1);

namespace Contributte\Tester;

use Contributte\Tester\Utils\FileSystem;
use RuntimeException;
use Tester\Environment as TesterEnvironment;

class Environment
{

	public const DEFAULT_TIMEZONE = 'Europe/Prague';

	private static string $cwd;

	private static string $tmpDir;

	private static string $testDir;

	public static function setup(string $cwd): void
	{
		self::$cwd = $cwd;

		self::setupTester();
		self::setupTimezone(self::DEFAULT_TIMEZONE);
		self::setupGlobalVariables();
		self::setupFolders(self::getCwd());
		self::setupSessions(self::getTmpDir());
	}

	public static function setupTester(): void
	{
		TesterEnvironment::setup();
	}

	public static function setupFunctions(): void
	{
		TesterEnvironment::setupFunctions();
	}

	public static function setupTimezone(string $timezone): void
	{
		date_default_timezone_set($timezone);
	}

	public static function setupFolders(string $dir): void
	{
		if (!is_dir($dir)) {
			throw new RuntimeException(sprintf('Provide existing folder, "%s" does not exist.', $dir));
		}

		self::$tmpDir = $dir . '/tmp';
		clearstatcache(true, self::$tmpDir);
		FileSystem::mkdir(self::$tmpDir);

		self::$testDir = self::$tmpDir . '/' . getmypid();
		clearstatcache(true, self::$testDir);
		FileSystem::mkdir(self::$testDir);

		// Drop testDir after all activities
		register_shutdown_function(function (): void {
			FileSystem::purge(self::$testDir);
			FileSystem::rmdir(self::$testDir);
		});
	}

	public static function setupSessions(string $dir): void
	{
		ini_set('session.save_path', $dir);
	}

	/**
	 * Configure global variables
	 */
	public static function setupGlobalVariables(): void
	{
		// @phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$_SERVER = array_intersect_key($_SERVER, array_flip([
			'PHP_SELF',
			'SCRIPT_NAME',
			'SERVER_ADDR',
			'SERVER_SOFTWARE',
			'HTTP_HOST',
			'DOCUMENT_ROOT',
			'OS',
			'argc',
			'argv',
		]));

		// @phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$_SERVER['REQUEST_TIME'] = 1234567890;

		// @phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$_ENV = $_GET = $_POST = [];
	}

	public static function ensureFolder(string $dir): void
	{
		clearstatcache(true, $dir);
		FileSystem::mkdir($dir);
		FileSystem::purge($dir);
	}

	public static function getCwd(): string
	{
		return self::$cwd;
	}

	public static function getTmpDir(): string
	{
		return self::$tmpDir;
	}

	public static function getTestDir(): string
	{
		return self::$testDir;
	}

	public static function skip(string $message): void
	{
		TesterEnvironment::skip($message);
	}

}
