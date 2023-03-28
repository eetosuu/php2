<?php

namespace Geekbrains\Php2\Blog\Repositories\PostsRepository;

use Geekbrains\Php2\Blog\Post;
use Geekbrains\Php2\Blog\UUID;

interface PostsRepositoryInterface
{
    public function get(UUID $uuid): Post;
    public function save(Post $post): void;
    public function delete(UUID $uuid): void;
}