<?php

namespace Geekbrains\Php2\Http\Actions\Auth;

use DateTimeImmutable;
use Geekbrains\Php2\Blog\Exceptions\AuthTokenNotFoundException;
use Geekbrains\Php2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\Auth\AuthException;
use Geekbrains\Php2\Http\Auth\BearerTokenAuthentication;
use Geekbrains\Php2\Http\Auth\PasswordAuthenticationInterface;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\SuccessfulResponse;

class LogOut implements ActionInterface
{
    public function __construct(
        private AuthTokensRepositoryInterface   $authTokensRepository,
        private BearerTokenAuthentication $authentication
    )
    {
    }

    /**
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {
            $token = $this->authentication->getTokenStr($request);

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }

        $authToken->setExpiresOn(new DateTimeImmutable('now'));

        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => $authToken->token(),
        ]);
    }
}