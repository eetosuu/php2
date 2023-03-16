<?php

use Geekbrains\Php2\Blog\Exceptions\AppException;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Php2\Http\Actions\Comments\CreateComment;
use Geekbrains\Php2\Http\Actions\Likes\CreateLike;
use Geekbrains\Php2\Http\Actions\Posts\CreatePost;
use Geekbrains\Php2\Http\Actions\Posts\DeletePost;
use Geekbrains\Php2\Http\Actions\Users\CreateUser;
use Geekbrains\Php2\Http\Actions\Users\FindByUsername;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Php2\Http\SuccessfulResponse;
use Geekbrains\Php2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;

$container = require __DIR__ . '/bootstrap.php';


$request = new Request($_GET,
    $_SERVER,
    file_get_contents('php://input'));

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
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
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
$response->send();

