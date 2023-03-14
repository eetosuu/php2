<?php

namespace Geekbrains\Php2\Http\Actions\Comments;

use Geekbrains\Php2\Blog\Comment;
use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\Post;
use Geekbrains\Php2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\SuccessfulResponse;

class CreateComment implements ActionInterface
{
    public function __construct(private CommentsRepositoryInterface $commentsRepository,
                                private PostsRepositoryInterface    $postsRepository,
                                private UserRepositoryInterface     $usersRepository,
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
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $newCommentUuid = UUID::random();
        try {
            $comment = new Comment(
                $newCommentUuid,
                $user,
                $post,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->commentsRepository->save($comment);
        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid,
        ]);
    }
}