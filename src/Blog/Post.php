<?php

namespace Geekbrains\Php2\Blog;


class Post
{

    public function __construct(private int $id,
                                private User $authorId,
                                private string $header,
                                private string $text)
    {

    }

    public function __toString(): string
    {
        return "Пользователь {$this->authorId->getId()} написал статью $this->id $this->header с содержанием: \n $this->text" . PHP_EOL;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId->getId();
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