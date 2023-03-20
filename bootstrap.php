<?php

use Dotenv\Dotenv;
use Geekbrains\Php2\Blog\Container\DIContainer;
use Geekbrains\Php2\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Geekbrains\Php2\Http\Auth\IdentificationInterface;
use Geekbrains\Php2\Http\Auth\JsonBodyUuidIdentification;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$logger = (new Logger('blog'));
if ('yes' === $_SERVER['LOG_TO_FILES']) {
    $logger->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
    ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}
if ('yes' === $_SERVER['LOG_TO_CONSOLE']) {
    $logger
        ->pushHandler(
            new StreamHandler("php://stdout")
        );
}

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
);
$container->bind(
    LoggerInterface::class,
    $logger
);
$container->bind(
    IdentificationInterface::class,
    JsonBodyUuidIdentification::class
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