<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Psr\Log\LoggerInterface;

#[AsCommand(
    name: 'app:add-user',
    description: 'Create user',
)]
class AddUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $encode;
    private UserRepository $userRepository;

    public function __construct( string $name = null, EntityManagerInterface $entityManager, UserPasswordHasherInterface $encode, UserRepository $userRepository, LoggerInterface $logger){

        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->encode = $encode;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', 'em',InputArgument::REQUIRED, 'Email')
            ->addOption('password', 'pa', InputArgument::OPTIONAL, 'Password')
            ->addOption('isAdmin', '',InputArgument::OPTIONAL, 'Если установлено, пользователь создается как администратор', 0)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $isAdmin = $input->getOption('isAdmin');

        $io->title('Добавление нового пользователя');
        $io->text(['Введите информацию']);

        if (!$email)
        {
            $email = $io->ask('Email (не указан), введите его');
        }

        if (!$password)
        {
            $password = $io->askHidden('Пароль (не указан), введите его');
        }

        if (!$isAdmin){
            $question = new Question('Хотите добавить роль админа? 1 - да, 0 - нет', 0);
            $isAdmin = $io->askQuestion($question);
        }

        $isAdmin = boolval($isAdmin);

        try {
            $user = $this->createUser($email, $password, $isAdmin);
            $user->getId();
        } catch (RuntimeException $exception) {
            $io->comment($exception->getMessage());

            return Command::FAILURE;
        }

        $successMessage = sprintf('Успешное добавление пользователя');

        $io->success($successMessage);

        return Command::SUCCESS;
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool $isAdmin
     * @return User
     */
    private function createUser(string $email, string $password, bool $isAdmin): User
    {
        $existUser = $this->userRepository->findOneBy(['email' => $email]);

        if($existUser){
            throw new RuntimeException('Такой пользователь уже существует');
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$isAdmin ? 'ROLE_ADMIN' : 'ROLE_USER']);

        $encoderPassword =$this->encode->hashPassword($user, $password);
        $user->setPassword($encoderPassword);

        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
