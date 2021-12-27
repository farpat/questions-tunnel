<?php

namespace App\Command;

use App\Entity\User;
use App\Service\Security\PasswordGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin-user',
    description: 'Create an admin user by passing email. The password is sent by mail',
)]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private PasswordGeneratorInterface $passwordGenerator,
        private MailerInterface $mailer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $user = $this->createUser(
                $input->getArgument('email'),
                $generatedPassword = $this->passwordGenerator->generate()
            );

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->sendMail($user, $generatedPassword);
        } catch (\Throwable $throwable) {
            $io->error($throwable->getMessage());
            return Command::FAILURE;
        }

        $io->success("The user << {$user->getEmail()} >> is created!");
        return Command::SUCCESS;
    }


    private function createUser(string $email, string $generatedPassword): User
    {
        $user = new User;
        return $user
            ->setEmail($email)
            ->setPassword($this->passwordHasher->hashPassword($user, $generatedPassword))
            ->setRoles(['ROLE_ADMIN']);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    private function sendMail(User $user, string $generatedPassword): void
    {
        $email = (new TemplatedEmail)
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject("The user << {$user->getEmail()} >> is created!")
            ->htmlTemplate('_emails/account_creation_confirmation.html.twig')
            ->textTemplate('_emails/account_creation_confirmation.text.twig')
            ->context([
                'generated_password' => $generatedPassword,
                'user' => $user,
            ]);

        $this->mailer->send($email);
    }
}
