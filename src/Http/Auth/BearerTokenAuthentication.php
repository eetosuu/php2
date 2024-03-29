<?php

namespace Geekbrains\Php2\Http\Auth;

use DateTimeImmutable;
use Geekbrains\Php2\Blog\Exceptions\AuthTokenNotFoundException;
use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Geekbrains\Php2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Http\Request;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{

    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
// Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository,
// Репозиторий пользователей
        private UserRepositoryInterface       $usersRepository,
    )
    {
    }

    /**
     * @throws AuthException
     */
    public  function getTokenStr(Request $request): string
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        // Проверяем, что заголовок имеет правильный формат
        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }
// Отрезаем префикс Bearer
        return mb_substr($header, strlen(self::HEADER_PREFIX));
    }



    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
// Отрезаем префикс Bearer
        $token = $this->getTokenStr($request);
// Ищем токен в репозитории
        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }
// Проверяем срок годности токена
        if ($authToken->expiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }
// Получаем UUID пользователя из токена
        $userUuid = $authToken->userUuid();
// Ищем и возвращаем пользователя
        return $this->usersRepository->get($userUuid);
    }
}