<?php

namespace Geekbrains\Php2\Blog\Repositories\UsersRepository;

use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Blog\Name;
use \PDO;
use \PDOStatement;

class SqliteUsersRepository implements UserRepositoryInterface
{
    public function __construct(private PDO $connection)
    {
    }

    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (first_name, last_name, uuid, username) VALUES (:first_name, :last_name, :uuid, :username)');
        $statement->execute([
            ':first_name' => $user->name()->firstname(),
            ':last_name' => $user->name()->lastname(),
            ':uuid' => (string)$user->uuid(),
            ':username' => $user->username(),
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getUser($statement, $uuid);
    }

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );
        $statement->execute(
            [
                ':username' => $username,
            ]);

        return $this->getUser($statement, $username);
    }

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    private function getUser(PDOStatement $statement, string $parameter): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new UserNotFoundException(
                "Cannot find user: $parameter"
            );
        }
        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username'],
        );
    }
}