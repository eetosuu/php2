<?php

namespace Geekbrains\Php2\Blog\Repositories\PostsRepository;

use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\Post;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Php2\Blog\UUID;
use PDO;
use PDOStatement;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = ?'
        );
        $statement->execute([
            (string)$uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }
    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text) VALUES (:uuid, :author_uuid, :title,  :text)');
        $statement->execute([
            ':uuid' => (string)$post->uuid(),
            ':author_uuid' => (string)$post->getUser()->uuid(),
            ':title' => $post->getHeader(),
            ':text' => $post->getText(),
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException|UserNotFoundException
     */
    private function getPost(PDOStatement $statement, string $parameter): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new PostNotFoundException(
                "Cannot find post: $parameter"
            );
        }

        $userRepository = new SqliteUsersRepository($this->connection);
        $user = $userRepository->get(new UUID($result['author_uuid']));
        return new Post(
            new UUID($result['uuid']),
            $user, $result['title'],
            $result['text']
        );
    }
}