<?php

namespace Geekbrains\Php2\Blog\Repositories\LikesRepository;

use Geekbrains\Php2\Blog\Like;
use Geekbrains\Php2\Blog\UUID;

interface LikesRepositoryInterface
{
    public function save(Like $like): void;

    public function getByPostUuid(UUID $post_uuid): array;

}