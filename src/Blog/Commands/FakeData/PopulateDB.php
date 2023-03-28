<?php

namespace Geekbrains\Php2\Blog\Commands\FakeData;

use Faker\Generator;
use Geekbrains\Php2\Blog\Comment;
use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Name;
use Geekbrains\Php2\Blog\Post;
use Geekbrains\Php2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Geekbrains\Php2\Blog\User;
use Geekbrains\Php2\Blog\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{

    public function __construct(
        private Generator                   $faker,
        private UserRepositoryInterface     $usersRepository,
        private PostsRepositoryInterface    $postsRepository,
        private CommentsRepositoryInterface $commentsRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption(
                //полное название опции --users-number
                'users-number',
                //сокращенное название опции -u
                'u',
                //необязательная опция
                InputOption::VALUE_OPTIONAL,
                'Users count',
            )
            ->addOption(
                'posts-number',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Posts count',
            );
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(
        InputInterface  $input,
        OutputInterface $output,
    ): int
    {
        //получаем значения опций из команды
        $usersCountInput = $input->getOption('users-number');
        $postsCountInput = $input->getOption('posts-number');
        //если значение опции пустое, то значение по-умолчанию
        $usersCount = empty($usersCountInput) ? 10 : $usersCountInput;
        $postsCount = empty($postsCountInput) ? 20 : $postsCountInput;
        $users = [];
        for ($i = 0; $i < $usersCount; $i++) {
            //создаем переданное кол-во юзеров через опцию
            //сохраняем в массив
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->username());
        }
        // От имени каждого пользователя
        // создаем кол-во постов, введенное в команде -p
        $posts = [];
        foreach ($users as $user) {
            for ($i = 0; $i < $postsCount; $i++) {
                $post = $this->createFakePost($user);
                //Сохраняем пост в массив, чтобы к ним создать комментарии
                $posts[] = $post;
                $output->writeln('Post created: ' . $post->getHeader());
            }
            foreach ($posts as $post) {
                //перебираем массив и создаем к каждому посту по 2 комментария
                for ($i = 0; $i < 2; $i++) {
                    $comment = $this->createFakeComment($user, $post);
                    $output->writeln('Comment created: ' . $comment->getText());
                }
            }
        }
        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user = User::createFrom(
        // Генерируем имя пользователя
            $this->faker->userName,
        // Генерируем пароль
            $this->faker->password,
            new Name(
        // Генерируем имя
                $this->faker->firstName,
        // Генерируем фамилию
                $this->faker->lastName
            )
        );
        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);
        return $user;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
        // Генерируем предложение не длиннее шести слов
            $this->faker->sentence(6, true),
        // Генерируем текст
            $this->faker->realText
        );
        // Сохраняем статью в репозиторий
        $this->postsRepository->save($post);
        return $post;
    }

    private function createFakeComment(User $author, Post $post): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $author,
            $post,
            //Предложение не длиннее 6 слов
            $this->faker->sentence(6, true),
        );
        // Сохраняем комментарий в репозиторий
        $this->commentsRepository->save($comment);
        return $comment;
    }
}