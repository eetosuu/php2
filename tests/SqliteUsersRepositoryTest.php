<?php

namespace Geekbrains\Php2;

use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\Name;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\UnitTests\DummyLogger;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteUsersRepository($connectionMock, new DummyLogger());
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Cannot find user: Ivan');

        $repository->getByUsername('Ivan');

    }

    public function testItSavesUserToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':username' => 'ivan123',
                ':first_name' => 'Ivan',
                ':last_name' => 'Nikitin',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

// 1. Передаём в репозиторий стаб подключения
        $repository = new SqliteUsersRepository($connectionStub, new DummyLogger());
// Вызываем метод сохранения пользователя
        $repository->save(
            new User( // Свойства пользователя точно такие,
// как и в описании мока
                new UUID('123e4567-e89b-12d3-a456-426614174000'), new Name('Ivan', 'Nikitin'),
                'ivan123',

            )
        );
    }
}