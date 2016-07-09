<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 11.11.2014
 * Time: 10:53
 */

namespace NemesisPlatform\Core\Account\Tests;

use NemesisPlatform\Components\Testing\FixtureTestCase;
use NemesisPlatform\Core\Account\Entity\User;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Role\RoleInterface;

class WebFixtureTestCase extends FixtureTestCase
{
    public function setUp()
    {
        parent::setUp();
        static::$client = static::createClient();
    }

    /**
     * @param string          $email email for logging in
     * @param RoleInterface[] $roles Roles to be set for user
     */
    protected function logIn($email, $roles = ['ROLE_USER'])
    {
        $session = $this->getClient()->getContainer()->get('session');

        $user = $this->getClient()->getContainer()->get('doctrine.orm.entity_manager')->getRepository(
            User::class
        )->findOneBy(['email' => $email]);

        $firewall = 'secured_area';
        $token    = new UsernamePasswordToken($user, null, $firewall, $roles);
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->getClient()->getCookieJar()->set($cookie);
    }

    public function getClient()
    {
        return static::$client;
    }
}
