<?php
include "vendor/autoload.php";

$faker = Faker\Factory::create();

switch ($argv[1]) {

    case 'user':
        $user = new \Geekbrains\Php2\Blog\User('1', $faker->firstName(), $faker->lastName());
        echo $user;
        break;
    case 'post':
        $user = new \Geekbrains\Php2\Blog\User('1', $faker->firstName(), $faker->lastName());
        $post = new \Geekbrains\Php2\Blog\Post(1, $user, $faker->text(5), $faker->text(1000));
        echo $post;
        break;
    case 'comment':
        $user = new \Geekbrains\Php2\Blog\User('1', $faker->firstName(), $faker->lastName());
        $post = new \Geekbrains\Php2\Blog\Post(1, $user, $faker->text(5), $faker->text(1000));
        $comment = new \Geekbrains\Php2\Blog\Comment(1, $user, $post, $faker->text(100));
        echo $comment;
}