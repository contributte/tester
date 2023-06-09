<?php declare(strict_types = 1);

namespace Contributte\Tester\Utils;

use Contributte\Tester\Environment;
use Nette\DI\Compiler;
use Nette\DI\Container as NetteContainer;
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

	public function build(): NetteContainer
	{
		$loader = new ContainerLoader($this->getTempDir(), true);
		$class = $loader->load(function (Compiler $compiler): void {
			foreach ($this->onCompile as $cb) {
				$cb($compiler);
			}
		}, $this->key);

		return new $class();
	}

	private function getTempDir(): string
	{
		if ($this->tempDir === null) {
			return Environment::getTmpDir();
		}

		return $this->tempDir;
	}

}
