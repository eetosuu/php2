<?php

namespace Geekbrains\Php2\Blog;

class Like
{

    public function __construct(private UUID $likeUuid, private UUID $postUuid, private UUID $authorUuid)
    {
    }

    /**
     * @return UUID
     */
    public function getLikeUuid(): UUID
    {
        return $this->likeUuid;
    }

    /**
     * @return UUID
     */
    public function getPostUuid(): UUID
    {
        return $this->postUuid;
    }

    /**
     * @param UUID $postUuid
     */
    public function setPostUuid(UUID $postUuid): void
    {
        $this->postUuid = $postUuid;
    }

    /**
     * @return UUID
     */
    public function getAuthorUuid(): UUID
    {
        return $this->authorUuid;
    }

    /**
     * @param UUID $authorUuid
     */
    public function setAuthorUuid(UUID $authorUuid): void
    {
        $this->authorUuid = $authorUuid;
    }

}