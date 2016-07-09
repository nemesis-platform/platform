<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 08.07.2015
 * Time: 10:53
 */

namespace NemesisPlatform\Core\Account\Features\Context;

use NemesisPlatform\Components\Test\Behat\Context\SymfonyContext;
use NemesisPlatform\Core\Account\Entity\Phone;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Rule\Participant\ConfirmedPhoneRule;
use NemesisPlatform\Game\Entity\Season;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CoreContext extends SymfonyContext
{
    private $data = [];

    /**
     * @Given /^I should receive an email for "([^"]*)"$/
     * @param string $email
     */
    public function iShouldReceiveAnEmailFor($email)
    {
        /** @var MessageDataCollector $collector */
        $collector = $this->getSymfonyProfile()->getCollector('swiftmailer');

        $messages = $collector->getMessages();

        \PHPUnit_Framework_Assert::assertEquals(
            1,
            $collector->getMessageCount(),
            'Invalid number of messages delivered'
        );

        /** @var \Swift_Message $message */
        $message = $messages[0];

        \PHPUnit_Framework_Assert::assertEquals(
            $email,
            key($message->getTo()),
            'Invalid message recipient'
        );

        $this->data['email'] = $message;
    }

    /**
     * @When /^I follow the email url$/
     */
    public function iFollowTheEmailUrl()
    {
        $this->getSession()->visit($this->locatePath($this->data['confirmation-url']));
    }

    /**
     * @Given /^email should contain url$/
     */
    public function emailShouldContainUrl()
    {
        /** @var \Swift_Message $message */
        $message = $this->data['email'];

        $messageCrawler = new Crawler();
        $messageCrawler->addContent($message->getBody());
        $url = $messageCrawler->filter('a[id="confirmation-link"]')->attr('href');

        \PHPUnit_Framework_Assert::assertNotNull($url, 'No confirmation url obtained');

        $this->data['confirmation-url'] = $url;
    }

    /**
     * @Given /^user "([^"]*)" has password "([^"]*)"$/
     * @param $email
     * @param $password
     */
    public function userHasPassword($email, $password)
    {
        $container = $this->getContainer();
        $manager   = $container->get('doctrine.orm.entity_manager');
        $user      = $manager->getRepository(
            User::class
        )->findOneBy(['email' => $email]);

        $encoder = $this->getContainer()->get('security.encoder_factory')->getEncoder($user);
        $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
        $manager->flush();
    }

    /**
     * @Given /^user "([^"]*)" has property "([^"]*)" set to "([^"]*)"$/
     * @param string $email user email
     * @param string $property
     * @param mixed  $value
     */
    public function setUserProperty($email, $property, $value)
    {
        $accessor  = new PropertyAccessor();
        $container = $this->getContainer();
        $manager   = $container->get('doctrine.orm.entity_manager');
        $user      = $manager->getRepository(User::class)->findOneBy(['email' => $email]);

        \PHPUnit_Framework_Assert::assertNotNull($user, 'Unknown user with email '.$email);

        $accessor->setValue($user, $property, $value);

        $manager->flush();
    }

    /**
     * @When /^I obtain confirmation link for "([^"]*)"$/
     * @param $email
     */
    public function iObtainConfirmationLinkFor($email)
    {
        $container = $this->getContainer();
        $manager   = $container->get('doctrine.orm.entity_manager');
        /** @var User $user */
        $user = $manager->getRepository(User::class)->findOneBy(['email' => $email]);

        \PHPUnit_Framework_Assert::assertNotNull($user, 'Unknown user with email '.$email);

        $this->data['confirmation-url'] = $container->get('router')->generate(
            'site_service_check_email',
            ['code' => $user->getCode()],
            RouterInterface::ABSOLUTE_PATH
        );
    }

    /**
     * @Given /^season "([^"]*)" requires mobile phone$/
     * @param $seasonName
     */
    public function seasonRequiresMobilePhone($seasonName)
    {
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $season  = $manager->getRepository(Season::class)->findOneBy(['name' => $seasonName]);
        \PHPUnit_Framework_Assert::assertNotNull($season);

        $rule    = new ConfirmedPhoneRule();
        $rules   = $season->getRules();
        $rules[] = $rule;
        $season->setRules($rules);

        $manager->persist($rule);
        $manager->flush();
    }

    /**
     * @Given /^user "([^"]*)" has confirmed phone "([^"]*)"$/
     */
    public function userHasConfirmedPhone($email, $phonenumber)
    {
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $user    = $manager->getRepository(User::class)->findOneBy(['email' => $email]);

        \PHPUnit_Framework_Assert::assertNotNull($user);

        $phone = new Phone();
        $phone->setPhonenumber($phonenumber);
        $phone->setFirstConfirmed(new \DateTime());
        $phone->setUser($user);
        $phone->setStatus(Phone::STATUS_ACTIVE);

        $user->setPhone($phone);

        $manager->persist($phone);
        $manager->flush();
    }

    /**
     * @param $username
     *
     * @return UserInterface
     */
    protected function getUserByUsername($username)
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository(
            User::class
        )->findOneBy(['email' => $username]);
    }
}
