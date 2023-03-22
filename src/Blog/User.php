<?php

namespace Geekbrains\Php2\Blog;

class User
{
    /**
     * @param UUID $uuid
     * @param Name $name
     * @param string $username
     * @param string $hashedPassword
     */
    public function __construct(private UUID   $uuid,
                                private Name   $name,
                                private string $username,
                                private string $hashedPassword
    )
    {
    }


    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }

    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

    private static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256', $uuid . $password);
    }

// Функция для проверки предъявленного пароля
    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->uuid);
    }

    public static function createFrom(
        string $username,
        string $password,
        Name   $name
    ): self
    {
        $uuid = UUID::random();
        return new self(
            $uuid,
            $name,
            $username,
            self::hash($password, $uuid)
        );
    }

}