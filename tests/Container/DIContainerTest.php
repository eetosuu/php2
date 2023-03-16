<?php

namespace Geekbrains\Php2\UnitTests\Container;

use Geekbrains\Php2\Blog\Container\DIContainer;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\InMemoryUsersRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DIContainerTest extends TestCase
{
    public function testItResolvesClassWithoutDependencies(): void
    {
// Создаём объект контейнера
        $container = new DIContainer();
        // Пытаемся получить объект класса без зависимостей
        $object = $container->get(SomeClassWithoutDependencies::class);
// Проверяем, что объект, который вернул контейнер,
// имеет желаемый тип
        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object
        );
    }

    public function testItResolvesClassByContract(): void
    {
        $container = new DIContainer();
        $container->bind(
            UserRepositoryInterface::class,
            InMemoryUsersRepository::class
        );
        $object = $container->get(UserRepositoryInterface::class);
        $this->assertInstanceOf(
            InMemoryUsersRepository::class,
            $object
        );
    }

    public function testItReturnsPredefinedObject(): void
    {
        $container = new DIContainer();
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );
// Пытаемся получить объект типа SomeClassWithParameter
        $object = $container->get(SomeClassWithParameter::class);
// Проверяем, что контейнер вернул
// объект того же типа
        $this->assertInstanceOf(
            SomeClassWithParameter::class,
            $object
        );
// Проверяем, что контейнер вернул
// тот же самый объект
        $this->assertSame(42, $object->value());
    }

    public function testItResolvesClassWithDependencies(): void
    {
// Создаём объект контейнера
        $container = new DIContainer();
// Устанавливаем правило получения
// объекта типа SomeClassWithParameter
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );
// Пытаемся получить объект типа ClassDependingOnAnother
        $object = $container->get(ClassDependingOnAnother::class);
// Проверяем, что контейнер вернул
// объект нужного нам типа
        $this->assertInstanceOf(
            ClassDependingOnAnother::class,
            $object
        );
    }
}