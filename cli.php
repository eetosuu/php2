<?php


use Geekbrains\Php2\Blog\Commands\Arguments;
use Geekbrains\Php2\Blog\Commands\CreateUserCommand;
use Geekbrains\Php2\Blog\Exceptions\AppException;
use Geekbrains\Php2\Blog\Repositories\LikesRepository\LikesRepositoryInterface;


$container = require __DIR__ . '/bootstrap.php';

$command = $container->get(CreateUserCommand::class);

try {

    $likeRep = $container->get(LikesRepositoryInterface::class);
    $likes = $likeRep->getByPostUuid(new \Geekbrains\Php2\Blog\UUID('0b14af37-f4ef-4d84-8e5a-c36b5a2663ac'));

    print_r($likes); die();
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}

