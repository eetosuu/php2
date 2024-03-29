<?php

namespace Geekbrains\Php2\Blog\Repositories\LikesRepository;

use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Exceptions\LikeAlreadyExists;
use Geekbrains\Php2\Blog\Exceptions\LikeNotFoundException;
use Geekbrains\Php2\Blog\Like;
use Geekbrains\Php2\Blog\UUID;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteLikesRepository implements LikesRepositoryInterface
{
    public function __construct(private PDO $connection, private LoggerInterface $logger)
    {
    }

    public function save(Like $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO likes (uuid, post_uuid, author_uuid) VALUES (:uuid, :post_uuid, :author_uuid)');
        $statement->execute([
            ':uuid' => (string)$like->getLikeUuid(),
            ':post_uuid' => (string)$like->getPostUuid(),
            ':author_uuid' => (string)$like->getAuthorUuid(),
        ]);

        $this->logger->info("Like created: {$like->getLikeUuid()}");
    }

    /**
     * @throws InvalidArgumentException
     * @throws LikeNotFoundException
     */
    public function getByPostUuid(UUID $post_uuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_uuid = :post_uuid'
        );
        $statement->execute(
            [
                ':post_uuid' => (string)$post_uuid,
            ]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($result === false) {
            $message = "Cannot find like by post: $post_uuid";
            $this->logger->warning($message);

            throw new LikeNotFoundException(
                $message
            );
        }
        $likes = [];
        foreach ($result as $like) {
            $likes[] = new Like(
                new UUID($like['uuid']),
                new UUID($like['post_uuid']),
                new UUID($like['author_uuid']));
        }
        return $likes;
    }

    /**
     * @throws LikeAlreadyExists
     */
    public function checkUserLikeForPostExists($postUuid, $authorUuid): void
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_uuid = :postUuid AND author_uuid = :authorUuid'
        );

        $statement->execute(
            [
                ':postUuid' => $postUuid,
                ':authorUuid' => $authorUuid
            ]
        );

        $isExisted = $statement->fetch();

        if ($isExisted) {
            $messageLikeAlreadyExists = "The users like for this post already exists";

            $this->logger->warning($messageLikeAlreadyExists);
            throw new LikeAlreadyExists(
                $messageLikeAlreadyExists
            );
        }
    }
}