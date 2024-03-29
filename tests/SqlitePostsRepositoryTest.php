<?php

namespace Geekbrains\Php2;

use Geekbrains\Php2\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Php2\Blog\Name;
use Geekbrains\Php2\Blog\Post;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\UnitTests\DummyLogger;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionMock, new DummyLogger());

        $this->expectExceptionMessage("Cannot find post: 123e4567-e89b-12d3-a456-426614174000");
        $this->expectException(PostNotFoundException::class);
        $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));

    }

    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':title' => 'Ivan',
                ':text' => 'Nikitin']);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostsRepository($connectionStub, new DummyLogger());

        $user = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            new Name('first_name', 'last_name'),
            'name', '123'
        );

        $repository->save(
            new Post(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                $user,
                'Ivan',
                'Nikitin'
            )
        );
    }

    public function testItGetPostByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createStub(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '9dba7ab0-93be-4ff4-9699-165320c97694',
            'author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
            'title' => 'Заголовок',
            'text' => 'Какой-то текст',
            'username' => 'ivan123',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
            'password' => '123'
        ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRep= new SqlitePostsRepository($connectionStub, new DummyLogger());
        $post = $postRep->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));

        $this->assertSame('9dba7ab0-93be-4ff4-9699-165320c97694', (string)$post->uuid());
    }
}