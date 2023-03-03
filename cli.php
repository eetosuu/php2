<?php


use Geekbrains\Php2\Blog\Commands\Arguments;
use Geekbrains\Php2\Blog\Commands\CreateUserCommand;
use Geekbrains\Php2\Blog\Exceptions\AppException;
use Geekbrains\Php2\Blog\Exceptions\CommandException;
use Geekbrains\Php2\Blog\Name;
use Geekbrains\Php2\Blog\Post;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Blog\UUID;

require_once __DIR__ . '/vendor/autoload.php';
// Создаём объект SQLite-репозитория
$usersRepository = new SqliteUsersRepository(
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);
$command = new CreateUserCommand($usersRepository);
try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}
