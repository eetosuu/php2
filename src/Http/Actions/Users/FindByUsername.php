<?php

namespace Geekbrains\Php2\Http\Actions\Users;

use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\SuccessfulResponse;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;

class FindByUsername implements ActionInterface
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function handle(Request $request): Response
    {
        try {
// Пытаемся получить искомое имя пользователя из запроса
            $username = $request->query('username');
        } catch (HttpException $e) {
// Если в запросе нет параметра username -
// возвращаем неуспешный ответ,
// сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }
        try {
// Пытаемся найти пользователя в репозитории
            $user = $this->userRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
// Если пользователь не найден -
// возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }
// Возвращаем успешный ответ
        return new SuccessfulResponse([
            'username' => $user->username(),
            'name' => $user->name()->firstname() . ' ' . $user->name()->lastname(),
        ]);
    }

}