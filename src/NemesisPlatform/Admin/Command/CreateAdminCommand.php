<?php
/**
 * User: scaytrase
 * Created: 2016-05-02 10:43
 */

namespace NemesisPlatform\Admin\Command;

use NemesisPlatform\Core\Account\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

class CreateAdminCommand extends ContainerAwareCommand
{
    /** {@inheritDoc} */
    protected function configure()
    {
        $this
            ->setName('nemesis:admin:create-admin')
            ->setDescription('Create admin user')
            ->addArgument('email', InputArgument::REQUIRED)
            ->setHelp('Creates activated admin user');
    }

    /** {@inheritDoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->getUser($input, $output);
        $user->setStatus(User::EMAIL_CONFIRMED);

        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $manager->persist($user);
        $manager->flush();

        $locator     = new FileLocator($this->getContainer()->getParameter('kernel.root_dir'));
        $paramsFile  = $locator->locate('config/parameters.yml');
        $paramsArray = Yaml::parse(file_get_contents($paramsFile));

        if (!in_array($user->getUsername(), $paramsArray['parameters']['admin_usernames'], true)) {
            $paramsArray['parameters']['admin_usernames'][] = $user->getUsername();
            file_put_contents($paramsFile, Yaml::dump($paramsArray));
            $this->getContainer()->get('cache_clearer')->clear($this->getContainer()->getParameter('kernel.cache_dir'));
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return User
     */
    protected function getUser(InputInterface $input, OutputInterface $output)
    {
        $email   = $input->getArgument('email');
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        if (null !== ($user = $manager->getRepository(User::class)->findOneBy(['email' => $email]))) {

            return $user;
        }

        /** @var QuestionHelper $helper */
        $helper   = $this->getHelper('question');
        $question = new Question('Provide secret string for the new user: ', false);
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        if (false === ($password = $helper->ask($input, $output, $question))) {
            throw new \InvalidArgumentException('Provide the password to continue');
        }

        $encoder = $this->getContainer()->get('security.encoder_factory')->getEncoder(User::class);
        $user    = new User(
            $email,
            $encoder->encodePassword($password, null),
            $helper->ask($input, $output, new Question('Provide first name: ', 'John')),
            $helper->ask($input, $output, new Question('Provide last name: ', 'Doe'))
        );

        return $user;
    }

}
