<?php

namespace Geekbrains\Php2\Commands;

use Geekbrains\Php2\Blog\Commands\Arguments;
use Geekbrains\Php2\Blog\Commands\CreateUserCommand;
use Geekbrains\Php2\Blog\Exceptions\ArgumentsException;
use Geekbrains\Php2\Blog\Exceptions\CommandException;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\DummyUsersRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Blog\UUID;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
// Создаём объект команды
// У команды одна зависимость - UsersRepositoryInterface
        $command = new CreateUserCommand(new DummyUsersRepository()
// здесь должна быть реализация UsersRepositoryInterface
        );
// Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);
        // и его сообщение
        $this->expectExceptionMessage('User already exists: Ivan');
// Запускаем команду с аргументами
        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    public function testItRequiresFirstName(): void
    {
// $usersRepository - это объект анонимного класса,
// реализующего контракт UsersRepositoryInterface
// Передаём объект анонимного класса
// в качестве реализации UsersRepositoryInterface
        $command = new CreateUserCommand($this->makeUsersRepository());
// Ожидаем, что будет брошено исключение
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');
// Запускаем команду
        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    public function testItRequiresLastName(): void
    {
// Передаём в конструктор команды объект, возвращаемый нашей функцией
        $command = new CreateUserCommand($this->makeUsersRepository());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');
        $command->handle(new Arguments([
            'username' => 'Ivan',
// Нам нужно передать имя пользователя,
// чтобы дойти до проверки наличия фамилии
            'first_name' => 'Ivan',
        ]));
    }

    private function makeUsersRepository(): UserRepositoryInterface
    {
        return new class implements UserRepositoryInterface {
            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }

    public function testItSavesUserToRepository(): void
    {
// Создаём объект анонимного класса
        $usersRepository = new class implements UserRepositoryInterface {
// В этом свойстве мы храним информацию о том,
// был ли вызван метод save
            private bool $called = false;

            public function save(User $user): void
            {
// Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
// Этого метода нет в контракте UsersRepositoryInterface,
// но ничто не мешает его добавить.
// С помощью этого метода мы можем узнать,
// был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };
// Передаём наш мок в команду
        $command = new CreateUserCommand($usersRepository);
// Запускаем команду
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]));
// Проверяем утверждение относительно мока,
// а не утверждение относительно команды
        $this->assertTrue($usersRepository->wasCalled());
    }

}