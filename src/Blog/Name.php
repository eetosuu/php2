<?php

namespace Geekbrains\Php2\Blog;

class Name
{

    public function __construct(private string $firstname, private string $lastname)
    {
    }

    /**
     * @return string
     */
    public function firstname(): string
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
    public function lastname(): string
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