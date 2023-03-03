<?php

namespace Geekbrains\Php2\Blog;


class Comment
{

    public function __construct(private UUID $uuid, private User $user, private Post $postId, private string $text)
    {
    }

    public function __toString(): string
    {
        return "Пользователь {$this->user->username()} прокомментировал статью {$this->postId->uuid()}: \n $this->text" . PHP_EOL;
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
     * @return Post
     */
    public function getPostId(): Post
    {
        return $this->postId;
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