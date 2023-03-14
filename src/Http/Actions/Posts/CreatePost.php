<?php

namespace Geekbrains\Php2\Http\Actions\Posts;

use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\Post;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\SuccessfulResponse;


class CreatePost implements ActionInterface
{
// Внедряем репозитории статей и пользователей
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private UserRepositoryInterface $usersRepository,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
           $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $newPostUuid = UUID::random();
        try {
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->postsRepository->save($post);
        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}