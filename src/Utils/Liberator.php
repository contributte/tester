<?php declare(strict_types = 1);

namespace Contributte\Tester\Utils;

use ReflectionClass;

/**
 * @phpstan-consistent-constructor
 */
class Liberator
{

	private object $object;

	/** @var ReflectionClass<object> */
	private ReflectionClass $class;

	/**
	 * @param class-string $class
	 */
	public function __construct(object $object, string $class)
	{
		$this->object = $object;
		$this->class = new ReflectionClass($class);
	}

	public static function of(object $object): self
	{
		return new static($object, $object::class);
	}

	/**
	 * @param class-string $class
	 */
	public static function ofClass(object $object, string $class): self
	{
		return new static($object, $class);
	}

	public function __isset(string $name): bool
	{
		if (!$this->class->hasProperty($name)) {
			return false;
		}

		$property = $this->class->getProperty($name);
		$property->setAccessible(true);

		return /* $property->isInitialized($this->object) &&*/ $property->getValue($this->object) !== null;
	}

	public function __get(string $name): mixed
	{
		$property = $this->class->getProperty($name);
		$property->setAccessible(true);

		return $property->getValue($this->object);
	}

	public function __set(string $name, mixed $value): void
	{
		$property = $this->class->getProperty($name);
		$property->setAccessible(true);
		$property->setValue($this->object, $value);
	}

	/**
	 * @param mixed[] $args
	 */
	public function __call(string $name, array $args = []): mixed
	{
		$method = $this->class->getMethod($name);
		$method->setAccessible(true);

		return $method->invokeArgs($this->object, $args);
	}

}
