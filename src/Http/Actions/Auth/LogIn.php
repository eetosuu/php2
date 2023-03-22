<?php

namespace Geekbrains\Php2\Http\Actions\Auth;

use DateTimeImmutable;
use Geekbrains\Php2\Blog\AuthToken;
use Geekbrains\Php2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\Auth\AuthException;
use Geekbrains\Php2\Http\Auth\PasswordAuthenticationInterface;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\SuccessfulResponse;

class LogIn implements ActionInterface
{

    public function __construct(
// Авторизация по паролю
        private PasswordAuthenticationInterface $passwordAuthentication,
// Репозиторий токенов
        private AuthTokensRepositoryInterface   $authTokensRepository
    )
    {
    }

    public function handle(Request $request): Response
    {

// Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
// Генерируем токен
        $authToken = new AuthToken(bin2hex(random_bytes(40)),
            $user->uuid(),
// Срок годности - 1 день
            (new DateTimeImmutable())->modify('+1 day')
        );
// Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);
// Возвращаем токен
        return new SuccessfulResponse([
            'token' => $authToken->token(),
        ]);
    }
}