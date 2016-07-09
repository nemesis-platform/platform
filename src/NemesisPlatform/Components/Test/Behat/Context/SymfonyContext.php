<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-11-22
 * Time: 23:26
 */

namespace NemesisPlatform\Components\Test\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Driver\SahiDriver;
use Behat\Symfony2Extension\Driver\KernelDriver;
use PHPUnit_Framework_Assert;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class SymfonyContext extends RawSymfonyContext
{

    /**
     * @Given /^I submit form with button "([^"]*)"$/
     * @param           $button
     * @param TableNode $fields
     */
    public function iSubmitForm($button, TableNode $fields)
    {

        $data = array();

        foreach ($fields->getRowsHash() as $field => $value) {
            $data[$field] = $value;
        }

        $driver = $this->getSession()->getDriver();

        if (class_exists('Behat\Mink\Driver\SahiDriver') && ($driver instanceof SahiDriver)) {
            foreach ($data as $key => $value) {
                $js = "document.getElementsByName('$key')[0].value='$value'";
                $this->getSession()->executeScript($js);
            }

            $button = $this->fixStepArgument($button);
            $this->getSession()->getPage()->pressButton($button);

            return;
        }

        if ($driver instanceof KernelDriver) {
            /** @var Client $client */
            $client = $driver->getClient();
            $form = $client->getCrawler()->selectButton($button)->form($data);
            $client->submit($form);
        };


    }

    /**
     * Returns fixed step argument (with \\" replaced back to ").
     *
     * @param string $argument
     *
     * @return string
     */
    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }

    /**
     * @Given /^I logged in as "([^"]*)"$/
     * @When /^I log in as "([^"]*)"$/
     *
     * @param        $email
     */
    public function loginAs($email)
    {
        $this->logIn($email);
    }

    /**
     * @param                 $email
     * @param RoleInterface[] $roles
     */
    protected function logIn($email, $roles = array('ROLE_USER'))
    {
        $container = $this->getContainer();
        $session = $container->get('session');

        $user = $this->getUserByUsername($email);
        PHPUnit_Framework_Assert::assertNotNull($user, 'Unknown user with email ' . $email);

        $firewall = 'secured_area';
        $token = new UsernamePasswordToken($user, null, $firewall, $roles);
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $this->getSession()->setCookie($session->getName(), $session->getId());
    }

    /**
     * @param           $email
     * @param TableNode $roles
     * @When /^I log in as "([^"]*)" with roles:$/
     * @Given /^I logged in as "([^"]*)" with roles:$/
     */
    public function loginWithRoles($email, TableNode $roles = null)
    {
        $data = array('ROLE_USER');

        if ($roles) {
            foreach ($roles->getHash() as $field => $value) {
                $data[] = $value;
            }
        }

        $this->logIn($email, $data);
    }

    /**
     * @param $username
     * @return UserInterface
     */
    abstract protected function getUserByUsername($username);
}
