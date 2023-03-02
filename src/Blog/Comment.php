<?php

namespace Geekbrains\Php2\Blog;


class Comment
{

    public function __construct(private int $id, private User $authorId, private Post $postId, private $text)
    {
    }

    public function __toString(): string
    {
        return "Пользователь $this->authorId прокомментировал статью {$this->postId->getId()}: \n $this->text" . PHP_EOL;
    }
}