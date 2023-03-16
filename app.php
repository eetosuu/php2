<?php

use Geekbrains\Php2\Blog\Comment;
use Geekbrains\Php2\Blog\Exceptions\AppException;
use Geekbrains\Php2\Blog\Like;
use Geekbrains\Php2\Blog\Name;
use Geekbrains\Php2\Blog\Post;
use Geekbrains\Php2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Geekbrains\Php2\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Blog\UUID;

include "vendor/autoload.php";


$like = new Like(UUID::random(), new UUID('123e4567-e89b-12d3-a456-426614174000'), new UUID('1831eed3-3046-4f5f-8c12-a3da5aaf58e3'));
$likeRep = new SqliteLikesRepository(new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));

var_dump($likeRep->getByPostUuid(new UUID('123e4567-e89b-12d3-a456-426614174000')));

