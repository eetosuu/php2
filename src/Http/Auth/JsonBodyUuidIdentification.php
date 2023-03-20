<?php

namespace Geekbrains\Php2\Http\Auth;

use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Http\Request;

class JsonBodyUuidIdentification implements IdentificationInterface
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
// Получаем UUID пользователя из JSON-тела запроса;
// ожидаем, что корректный UUID находится в поле user_uuid
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
// Если невозможно получить UUID из запроса -
// бросаем исключение
            throw new AuthException($e->getMessage());
        }
        try {
// Ищем пользователя в репозитории и возвращаем его
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
// Если пользователь с таким UUID не найден -
// бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }

}