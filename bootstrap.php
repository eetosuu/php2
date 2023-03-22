<?php

use Dotenv\Dotenv;
use Geekbrains\Php2\Blog\Container\DIContainer;
use Geekbrains\Php2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use Geekbrains\Php2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Geekbrains\Php2\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Geekbrains\Php2\Http\Auth\AuthenticationInterface;
use Geekbrains\Php2\Http\Auth\BearerTokenAuthentication;
use Geekbrains\Php2\Http\Auth\JsonBodyUuidAuthentication;
use Geekbrains\Php2\Http\Auth\PasswordAuthentication;
use Geekbrains\Php2\Http\Auth\PasswordAuthenticationInterface;
use Geekbrains\Php2\Http\Auth\TokenAuthenticationInterface;
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
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);
$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);
$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
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
$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

return $container;