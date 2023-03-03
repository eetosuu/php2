<?php

namespace Geekbrains\Php2\Blog;


class Post
{

    public function __construct(private UUID $uuid,
                                private User $user,
                                private string $header,
                                private string $text)
    {

    }

    public function __toString(): string
    {
        return "Пользователь {$this->user->uuid()} написал статью $this->uuid $this->header с содержанием: \n $this->text" . PHP_EOL;
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader(string $header): void
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

}