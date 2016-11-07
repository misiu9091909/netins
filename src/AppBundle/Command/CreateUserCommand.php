<?php
/**
 * Created by PhpStorm.
 * User: misiu9091909
 * Date: 11/7/16
 * Time: 7:58 PM
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends ContainerAwareCommand
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    protected function configure()
    {
        $this
            ->setName('app:create-user')
            ->setDescription('Create an user')
            ->setHelp($this->getCommandHelp())
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addArgument('email', InputArgument::REQUIRED, 'Email');
        ;
    }

    /**
     * Initialize fields before command execution
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');

        $alreadyExistingUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (null !== $alreadyExistingUser) {
            throw new \RuntimeException(sprintf('User %s already exists.', $username));
        }

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $encodedPassword = $this->getContainer()->get('security.password_encoder')->encodePassword($user, $password);
        $user->setPassword($encodedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('', sprintf('User created, username: %s, email: %s', $user->getUsername(), $user->getEmail()));
    }


    private function getCommandHelp()
    {
        return "<info>%command.name%</info> command is for creating new users.\n" .
          "<info>php %command.full_name%</info> <comment>username password email</comment>\n";
    }
}
