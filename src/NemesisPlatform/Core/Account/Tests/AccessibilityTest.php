<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.08.2014
 * Time: 16:15
 */

namespace NemesisPlatform\Core\Account\Tests;

use NemesisPlatform\Core\CMS\Entity\MenuElement;

class AccessibilityTest extends WebFixtureTestCase
{
    /**
     * @param $url
     *
     * @dataset      NemesisPlatform\Core\Account\Tests\DataSet\SeasonDataset
     * @dataset      NemesisPlatform\Core\Account\Tests\DataSet\UsersDataset
     * @dataProvider accountUrlProvider
     */
    public function testAccountUrlsNotAccessibleByAnonymous($url)
    {
        self::$client->setServerParameters(['HTTP_HOST' => 'local.nemesis-project']);
        $this->getClient()->request('GET', $url);
        static::assertTrue($this->getClient()->getResponse()->isRedirection(), $this->getClient()->getResponse());
    }


    /**
     * @param $url
     *
     * @dataset      NemesisPlatform\Core\Account\Tests\DataSet\SeasonDataset
     * @dataset      NemesisPlatform\Core\Account\Tests\DataSet\UsersDataset
     * @dataProvider accountUrlProvider
     */
    public function testAccountUrlsAccessibleByUser($url)
    {
        self::$client->setServerParameters(['HTTP_HOST' => 'local.nemesis-project']);
        $this->logIn('user@test', ['ROLE_USER']);
        $this->getClient()->request('GET', $url);
        static::assertTrue($this->getClient()->getResponse()->isSuccessful(), $this->getClient()->getResponse());
    }

    public function accountUrlProvider()
    {
        static::bootKernel();

        return $this->populateLinks(
            $this->getContainer()->get('nemesis.registry.menu')->getMenu('profile_menu')
        );
    }

    /**
     * @param MenuElement[] $menu
     *
     * @return string[]
     */
    private function populateLinks($menu)
    {
        $links = [];

        foreach ($menu as $link) {
            if (!$link->isDisabled()) {
                if ($link->isDropdown()) {
                    foreach ($link->getChildren() as $child) {
                        if (!$child->isDisabled()) {
                            $links[$link->getLabel()."/".$child->getLabel()] = [$child->getLink()];
                        }
                    }
                } else {
                    $links[$link->getLabel()] = [$link->getLink()];
                }
            }
        }

        return $links;
    }
}
