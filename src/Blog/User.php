<?php
namespace Geekbrains\Php2\Blog;

class User
{
    /**
     * @param UUID $uuid
     * @param string $username
     * @param Name $name
     */
    public function __construct(private UUID   $uuid,
                                private Name   $name,
                                private string $username
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

}