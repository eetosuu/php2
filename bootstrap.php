<?php

use Geekbrains\Php2\Blog\Container\DIContainer;
use Geekbrains\Php2\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);
$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
);
$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);
$container->bind(
    UserRepositoryInterface::class,
    SqliteUsersRepository::class
);

return $container;