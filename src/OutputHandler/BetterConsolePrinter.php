<?php declare(strict_types = 1);

namespace Contributte\Tester\OutputHandler;

use Tester\Dumper;
use Tester\Runner\Output\ConsolePrinter;
use Tester\Runner\Test;

/**
 * phpcs:ignore
 * @copyright Martin Zlamal <http://zlml.cz/tester-vlastni-output-handler>
 */
class BetterConsolePrinter extends ConsolePrinter
{

	/**
	 * Display headline of testing
	 */
	public function begin(): void
	{
		ob_start();
		parent::begin();// phpcs:ignore
		echo rtrim((string) ob_get_clean()) . "\n\n";
	}

	/**
	 * Display result of single test
	 */
	public function result(string $testName, int $result, string $message): void
	{
		$outputs = [
			Test::PASSED => Dumper::color('green', '✔ ' . $testName),
			Test::SKIPPED => Dumper::color('olive', 's ' . $testName) . '(' . $message . ')',
			Test::FAILED => Dumper::color('red', '✖ ' . $testName) . "\n" . $this->indent($message, 3) . "\n",
		];
		echo $this->indent($outputs[$result], 2) . PHP_EOL;
	}

	/**
	 * Display result of testing
	 */
	public function end(): void
	{
		ob_start();
		parent::end();// phpcs:ignore
		echo "\n" . trim((string) ob_get_clean()) . "\n";
	}

	private function indent(string $message, int $spaces): string
	{
		if ($message !== '') {
			$result = '';

			foreach (explode(PHP_EOL, $message) as $line) {
				$result .= str_repeat(' ', $spaces) . $line . PHP_EOL;
			}

			return rtrim($result, PHP_EOL);
		}

		return $message;
	}

}
