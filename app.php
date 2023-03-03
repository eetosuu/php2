<?php

use Geekbrains\Php2\Blog\Comment;
use Geekbrains\Php2\Blog\Exceptions\AppException;
use Geekbrains\Php2\Blog\Name;
use Geekbrains\Php2\Blog\Post;
use Geekbrains\Php2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Blog\UUID;

include "vendor/autoload.php";

$faker = Faker\Factory::create();


$postsRepository = new SqlitePostsRepository(
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);
$user = new User(UUID::random(), new Name('Никита', 'Князев'), '86555');
$userRep = new SqliteUsersRepository(new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));
$userRep->save($user);
$comRep = new SqliteCommentsRepository(new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));
try {
    $post = new Post(UUID::random(), $user, 'Привет, мир', 'Это мой первый пост');
} catch (AppException $e) {
    echo $e->getMessage();
}
$postsRepository->save($post);
echo $postsRepository->get($post->uuid());
$com = new Comment(UUID::random(), $user, $post, 'ПРИВЕТ!!!');
$comRep->save($com);

