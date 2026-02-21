<?php declare(strict_types = 1);

use Contributte\Tester\Utils\ContainerBuilder;
use Contributte\Tester\Utils\ContainerPatcher;
use Contributte\Tester\Utils\Notes;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$container = ContainerBuilder::of(__FILE__)
	->withCompiler(function (Compiler $compiler): void {
		$compiler->addConfig([
			'services' => [
				'named' => ['create' => ArrayObject::class],
				'type.a' => ['create' => DateTimeImmutable::class],
				'type.b' => ['create' => DateTimeImmutable::class],
				'tag.one' => ['create' => stdClass::class, 'tags' => ['demo.tag' => true]],
				'tag.two' => ['create' => stdClass::class, 'tags' => ['demo.tag' => ['scope' => 'all']]],
			],
		]);
	})
	->build();

ContainerPatcher::of($container)->service('named', function (Container $passedContainer) use ($container): object {
	Assert::same($container, $passedContainer);
	Notes::add('service:named');

	return new ArrayObject(['named']);
});

Assert::same(['service:named'], Notes::fetch());
Assert::type(ArrayObject::class, $container->getService('named'));
Assert::equal(['named'], $container->getService('named')->getArrayCopy());

ContainerPatcher::of($container)->type(DateTimeImmutable::class, function (Container $container, string $name): object {
	Notes::add('type:' . $name);
	Assert::true($container instanceof Container);

	return new DateTimeImmutable('2024-01-01');
});

Assert::equal(['type:type.a', 'type:type.b'], Notes::fetch());
Assert::same('2024-01-01', $container->getService('type.a')->format('Y-m-d'));
Assert::same('2024-01-01', $container->getService('type.b')->format('Y-m-d'));

ContainerPatcher::of($container)->tag('demo.tag', function (Container $container, string $name): object {
	Notes::add('tag:' . $name);
	Assert::true($container instanceof Container);

	return new stdClass();
});

Assert::equal(['tag:tag.one', 'tag:tag.two'], Notes::fetch());
Assert::type(stdClass::class, $container->getService('tag.one'));
Assert::type(stdClass::class, $container->getService('tag.two'));

ContainerPatcher::of($container)->serviceInstance('named', new ArrayObject(['direct']));
Assert::equal(['direct'], $container->getService('named')->getArrayCopy());

Assert::exception(
	fn (): ContainerPatcher => ContainerPatcher::of($container)->serviceInstance('missing', new ArrayObject()),
	MissingServiceException::class,
	'Service named "missing" not found in container.',
);

Assert::exception(
	fn (): ContainerPatcher => ContainerPatcher::of($container)->type(SplObjectStorage::class, fn (): object => new SplObjectStorage()),
	MissingServiceException::class,
	'Service of type "SplObjectStorage" not found in container.',
);

Assert::exception(
	fn (): ContainerPatcher => ContainerPatcher::of($container)->tagInstance('missing.tag', new stdClass()),
	MissingServiceException::class,
	'Service tagged "missing.tag" not found in container.',
);
