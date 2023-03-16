<?php

namespace Geekbrains\Php2\Blog\Repositories\UsersRepository;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Blog\UUID;

class InMemoryUsersRepository implements UserRepositoryInterface
{
    private array $users = [];
    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @param UUID $uuid
     * @return User
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user) {
            if ($user->id() === $uuid) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $uuid");
    }

    public function getByUsername(string $username): User
    {
        // TODO: Implement getByUsername() method.
    }
}