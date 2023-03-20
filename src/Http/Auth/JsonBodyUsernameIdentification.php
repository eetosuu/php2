<?php

namespace Geekbrains\Php2\Http\Auth;

use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Blog\User;

class JsonBodyUsernameIdentification implements IdentificationInterface
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    )
    {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
// Получаем имя пользователя из JSON-тела запроса;
// ожидаем, что имя пользователя находится в поле username
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
// Если невозможно получить имя пользователя из запроса -
// бросаем исключение
            throw new AuthException($e->getMessage());
        }
        try {
// Ищем пользователя в репозитории и возвращаем его
            return $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
// Если пользователь не найден -
// бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }
}