<?php

namespace Geekbrains\Php2\Blog\Repositories\CommentsRepository;

use Geekbrains\Php2\Blog\Comment;
use Geekbrains\Php2\Blog\Exceptions\CommentNotFoundException;
use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Php2\Blog\UUID;
use PDO;
use PDOStatement;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = ?'
        );
        $statement->execute([
            (string)$uuid,
        ]);

        return $this->getComment($statement, $uuid);
    }

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text) VALUES (:uuid, :post_uuid, :author_uuid, :text)');
        $statement->execute([
            ':uuid' => (string)$comment->uuid(),
            ':author_uuid' => (string)$comment->getUser()->uuid(),
            ':post_uuid' => $comment->uuid(),
            ':text' => $comment->getText(),
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws CommentNotFoundException
     */
    private function getComment(PDOStatement $statement, string $parameter): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $userRepository = new SqliteUsersRepository($this->connection);
        $postRepository = new SqlitePostsRepository($this->connection);
        $user = $userRepository->get($result['author_uuid']);
        $post = $postRepository->get($result['post_uuid']);
        if ($result === false) {
            throw new CommentNotFoundException(
                "Cannot find comment: $parameter"
            );
        }
        return new Comment(
            new UUID($result['uuid']),
            $user, $post,
            $result['text']
        );
    }
}