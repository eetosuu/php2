<?php

namespace Geekbrains\Php2\Blog\Repositories\CommentsRepository;

use Geekbrains\Php2\Blog\Comment;
use Geekbrains\Php2\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function get(UUID $uuid): Comment;
    public function save(Comment $comment): void;


}