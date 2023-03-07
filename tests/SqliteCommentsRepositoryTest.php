<?php

namespace Geekbrains\Php2;

use Geekbrains\Php2\Blog\Comment;
use Geekbrains\Php2\Blog\Exceptions\CommentNotFoundException;
use Geekbrains\Php2\Blog\Name;
use Geekbrains\Php2\Blog\Post;
use Geekbrains\Php2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Blog\UUID;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionMock);

        $this->expectExceptionMessage("Cannot find comment: 123e4567-e89b-12d3-a456-426614174000");
        $this->expectException(CommentNotFoundException::class);
        $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));
    }

    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $repository = new SqliteCommentsRepository($connectionStub);

        $user = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            new Name('first_name', 'last_name'),
            'name',
        );

        $post = new Post(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                $user,
                'Ivan',
                'Nikitin'
            );

        $repository->save(new Comment(new UUID('123e4567-e89b-12d3-a456-426614174000'), $user, $post, 'ааааа'));

    }

    public function testItGetCommentByUuid(): void
    {
    }
}