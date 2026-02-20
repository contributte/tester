<?php declare(strict_types = 1);

namespace Contributte\Tester\Utils;

use Closure;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;
use ReflectionFunction;

final class ContainerPatcher
{

	private function __construct(
		private Container $container,
	)
	{
	}

	public static function of(Container $container): self
	{
		return new self($container);
	}

	/**
	 * @param Closure(Container): object|Closure(Container, string): object $factory
	 */
	public function service(string $name, Closure $factory): self
	{
		if (!$this->container->hasService($name)) {
			throw new MissingServiceException(sprintf('Service named "%s" not found in container.', $name));
		}

		return $this->replace([$name], $factory);
	}

	public function serviceInstance(string $name, object $service): self
	{
		if (!$this->container->hasService($name)) {
			throw new MissingServiceException(sprintf('Service named "%s" not found in container.', $name));
		}

		$this->container->removeService($name);
		$this->container->addService($name, $service);

		return $this;
	}

	/**
	 * @template T of object
	 * @param class-string<T> $type
	 * @param Closure(Container): T|Closure(Container, string): T $factory
	 */
	public function type(string $type, Closure $factory): self
	{
		$names = $this->container->findByType($type);

		if ($names === []) {
			throw new MissingServiceException(sprintf('Service of type "%s" not found in container.', $type));
		}

		return $this->replace($names, $factory);
	}

	public function typeInstance(string $type, object $service): self
	{
		$names = $this->container->findByType($type);

		if ($names === []) {
			throw new MissingServiceException(sprintf('Service of type "%s" not found in container.', $type));
		}

		foreach ($names as $name) {
			$this->container->removeService($name);
			$this->container->addService($name, $service);
		}

		return $this;
	}

	/**
	 * @param Closure(Container): object|Closure(Container, string): object $factory
	 */
	public function tag(string $tag, Closure $factory): self
	{
		$services = $this->container->findByTag($tag);

		if ($services === []) {
			throw new MissingServiceException(sprintf('Service tagged "%s" not found in container.', $tag));
		}

		return $this->replace(array_keys($services), $factory);
	}

	public function tagInstance(string $tag, object $service): self
	{
		$services = $this->container->findByTag($tag);

		if ($services === []) {
			throw new MissingServiceException(sprintf('Service tagged "%s" not found in container.', $tag));
		}

		foreach (array_keys($services) as $name) {
			$this->container->removeService($name);
			$this->container->addService($name, $service);
		}

		return $this;
	}

	/**
	 * @param array<string> $names
	 * @param Closure(Container): object|Closure(Container, string): object $factory
	 */
	private function replace(array $names, Closure $factory): self
	{
		foreach ($names as $name) {
			$service = $this->createService($factory, $name);
			$this->container->removeService($name);
			$this->container->addService($name, $service);
		}

		return $this;
	}

	private function createService(Closure $factory, string $name): object
	{
		$reflection = new ReflectionFunction($factory);

		if ($reflection->getNumberOfParameters() < 2) {
			return $factory($this->container);
		}

		return $factory($this->container, $name);
	}

}
