<?php


use Geekbrains\Php2\Blog\Commands\Arguments;
use Geekbrains\Php2\Blog\Commands\CreateUserCommand;
use Geekbrains\Php2\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;


$container = require __DIR__ . '/bootstrap.php';

$command = $container->get(CreateUserCommand::class);

$logger = $container->get(LoggerInterface::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
    $logger->error($e->getMessage(), ['exception' => $e]);
}

