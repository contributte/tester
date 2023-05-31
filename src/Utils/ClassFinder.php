<?php declare(strict_types = 1);

namespace Contributte\Tester\Utils;

use Nette\Loaders\RobotLoader;
use ReflectionClass;
use RuntimeException;

class ClassFinder
{

	/** @var string[] */
	private array $folders = [];

	/** @var callable[] */
	private array $callbacks = [];

	public static function create(): self
	{
		return new self();
	}

	public function addFolder(string $folder): self
	{
		$this->folders[] = $folder;

		return $this;
	}

	/**
	 * @param class-string $class
	 */
	public function includeSubclass(string $class): self
	{
		$this->callbacks[] = static fn (ReflectionClass $rc) => is_a($rc->getName(), $class, true);

		return $this;
	}

	/**
	 * @param class-string $class
	 */
	public function excludeSubclass(string $class): self
	{
		$this->callbacks[] = static fn (ReflectionClass $rc) => !is_a($rc->getName(), $class, true);

		return $this;
	}

	/**
	 * @return array<string, ReflectionClass<object>>
	 */
	public function find(): array
	{
		if (!class_exists(RobotLoader::class)) {
			throw new RuntimeException('RobotLoader not found, please install nette/robot-loader');
		}

		$loader = new RobotLoader();

		// Add folders
		foreach ($this->folders as $folder) {
			$loader->addDirectory($folder);
		}

		// Collect classes
		$loader->rebuild();

		// Get classes
		$classes = $loader->getIndexedClasses();

		// Iterate over classes
		$output = [];

		/** @var class-string $class */
		foreach ($classes as $class => $file) {
			$rc = new ReflectionClass($class);

			// Apply callbacks
			foreach ($this->callbacks as $callback) {
				$res = $callback($rc);

				// Skip class if callback is false
				if ($res === false) {
					continue 2;
				}
			}

			$output[$class] = $rc;
		}

		return $output;
	}

}
