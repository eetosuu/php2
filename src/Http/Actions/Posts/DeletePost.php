<?php

namespace Geekbrains\Php2\Http\Actions\Posts;

use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Geekbrains\Php2\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\Auth\AuthException;
use Geekbrains\Php2\Http\Auth\TokenAuthenticationInterface;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\SuccessfulResponse;

class DeletePost implements ActionInterface
{
    public function __construct(private PostsRepositoryInterface     $postsRepository,
                                private TokenAuthenticationInterface $authentication
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $postUuid = $request->query('uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $postUuid = new UUID($postUuid);
        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->postsRepository->delete($postUuid);
        return new SuccessfulResponse([
            'uuid' => (string)$postUuid,
        ]);

    }
}