<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.08.2014
 * Time: 16:15
 */

namespace NemesisPlatform\Admin\Tests;

use NemesisPlatform\Core\CMS\Entity\MenuElement;
use NemesisPlatform\Core\Account\Tests\WebFixtureTestCase;

class AccessibilityTest extends WebFixtureTestCase
{

    /**
     * @param $url
     *
     * @dataProvider adminUrlProvider
     * @dataset      NemesisPlatform\Core\Account\Tests\DataSet\SeasonDataset
     * @dataset      NemesisPlatform\Core\Account\Tests\DataSet\UsersDataset
     */
    public function testAdminUrlsAccessibleByAdmin($url)
    {
        $this->logIn('admin@test', ['ROLE_ADMIN']);
        $this->getClient()->request('GET', $url);
        $this->assertTrue($this->getClient()->getResponse()->isSuccessful(), $this->getClient()->getResponse());
    }

    /**
     * @param $url
     *
     * @dataProvider adminUrlProvider
     * @dataset      NemesisPlatform\Core\Account\Tests\DataSet\SeasonDataset
     * @dataset      NemesisPlatform\Core\Account\Tests\DataSet\UsersDataset
     */
    public function testAdminUrlsNotAccessibleByUser($url)
    {
        $this->logIn('user@test', ['ROLE_USER']);
        $this->getClient()->request('GET', $url);
        $this->assertTrue($this->getClient()->getResponse()->isForbidden(), $this->getClient()->getResponse());
    }

    public function adminUrlProvider()
    {
        static::bootKernel();

        return $this->populateLinks(
            $this->getContainer()->get('nemesis.registry.menu')->getMenu('admin_menu')
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
