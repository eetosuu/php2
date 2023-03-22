<?php

namespace Actions;

use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Blog\Name;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\Users\FindByUsername;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\SuccessfulResponse;
use JsonException;
use PHPUnit\Framework\TestCase;

class FindByUsernameActionTest extends TestCase
{
// Запускаем тест в отдельном процессе
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfNoUsernameProvided(): void
    {
// Создаём объект запроса
// Вместо суперглобальных переменных
// передаём простые массивы
        $request = new Request([], [], '');
        // Создаём стаб репозитория пользователей
        $usersRepository = $this->usersRepository([]);
//Создаём объект действия
        $action = new FindByUsername($usersRepository);
// Запускаем действие
        $response = $action->handle($request);
// Проверяем, что ответ - неудачный
        $this->assertInstanceOf(ErrorResponse::class, $response);
// Описываем ожидание того, что будет отправлено в поток вывода
        $this->expectOutputString(
            '{"success":false,"reason":"No such query param in the request: username"}');
// Отправляем ответ в поток вывода
        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
// Теперь запрос будет иметь параметр username
        $request = new Request(['username' => 'ivan'], [], '');
// Репозиторий пользователей по-прежнему пуст
        $usersRepository = $this->usersRepository([]);
        $action = new FindByUsername($usersRepository);
        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Not found"}');
        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws InvalidArgumentException|JsonException
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['username' => 'ivan'], [], '');
// На этот раз в репозитории есть нужный нам пользователь
        $usersRepository = $this->usersRepository([
            new User(
                UUID::random(),
                new Name('Ivan', 'Nikitin'),
                'ivan', '123'
            )
        ]);
        $action = new FindByUsername($usersRepository);
        $response = $action->handle($request);
// Проверяем, что ответ - удачный
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"username":"ivan","name":"Ivan Nikitin"}}');
        $response->send();
    }

    private function usersRepository(array $users): UserRepositoryInterface
    {// В конструктор анонимного класса передаём массив пользователей
        return new class($users) implements UserRepositoryInterface {
            public function __construct(
                private array $users
            )
            {
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $username === $user->username()) {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found");
            }
        };
    }
}