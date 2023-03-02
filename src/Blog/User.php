<?php
namespace Geekbrains\Php2\Blog;
use Geekbrains\Php2\Person\Name;

class User
{
    /**
     * @param int $id
     * @param string $firstname
     * @param string $lastname
     */
    public function __construct(private int    $id,
                                private string $firstname,
                                private string $lastname)
    {
    }

    public function __toString(): string
    {
        return "User $this->id с именем $this->firstname и фамилией $this->lastname." . PHP_EOL;
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
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

}