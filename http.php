<?php

use Geekbrains\Php2\Blog\Exceptions\AppException;
use Geekbrains\Php2\Http\Actions\Comments\CreateComment;
use Geekbrains\Php2\Http\Actions\Likes\CreateLike;
use Geekbrains\Php2\Http\Actions\Posts\CreatePost;
use Geekbrains\Php2\Http\Actions\Posts\DeletePost;
use Geekbrains\Php2\Http\Actions\Users\CreateUser;
use Geekbrains\Php2\Http\Actions\Users\FindByUsername;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Psr\Log\LoggerInterface;


$container = require __DIR__ . '/bootstrap.php';


$request = new Request($_GET,
    $_SERVER,
    file_get_contents('php://input'));
$logger = $container->get(LoggerInterface::class);
try {
    $path = $request->path();

} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}


$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
    ],
    'POST' => [
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/posts/comment' => CreateComment::class,
        '/posts/like' => CreateLike::class
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
    ],
];
if (!array_key_exists($method, $routes)
    || !array_key_exists($path, $routes[$method])) {
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
    return;
}
$response->send();

