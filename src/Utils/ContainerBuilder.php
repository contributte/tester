<?php declare(strict_types = 1);

namespace Contributte\Tester\Utils;

use Contributte\Tester\Environment;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;

class ContainerBuilder
{

	/** @var callable[] */
	private array $onCompile = [];

	private string|null $tempDir = null;

	private function __construct(private string $key)
	{
	}

	public static function of(?string $key = null): self
	{
		return new self($key ?? uniqid(random_bytes(16)));
	}

	public function withCompiler(callable $cb): self
	{
		$this->onCompile[] = static function (Compiler $compiler) use ($cb): void {
			$cb($compiler);
		};

		return $this;
	}

	public function withTempDir(string $tempDir): self
	{
		$this->tempDir = $tempDir;

		return $this;
	}

	public function build(): Container
	{
		return $this->buildContainer();
	}

	/**
	 * @param array<string, mixed> $parameters
	 */
	public function buildWith(array $parameters = []): Container
	{
		return $this->buildContainer($parameters);
	}

	/**
	 * @param array<string, mixed> $parameters
	 */
	private function buildContainer(array $parameters = []): Container
	{
		$loader = new ContainerLoader($this->getTempDir(), true);
		$class = $loader->load(function (Compiler $compiler) use ($parameters): void {
			foreach ($this->onCompile as $cb) {
				$cb($compiler);
			}

			$compiler->setDynamicParameterNames(array_keys($parameters));
		}, $this->key);

		/** @var Container $container */
		$container = new $class($parameters);

		return $container;
	}

	private function getTempDir(): string
	{
		if ($this->tempDir === null) {
			return Environment::getTestDir();
		}

		return $this->tempDir;
	}

}
