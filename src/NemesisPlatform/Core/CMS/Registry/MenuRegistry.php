<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-08-17
 * Time: 22:08
 */

namespace NemesisPlatform\Core\CMS\Registry;

use ArrayAccess;
use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Components\MultiSite\Service\SiteManagerService;
use NemesisPlatform\Core\CMS\Entity\Menu;
use NemesisPlatform\Core\CMS\Entity\MenuElement;

class MenuRegistry implements ArrayAccess
{

    /** @var  \NemesisPlatform\Core\CMS\Entity\MenuElement[] */
    private $menuEntries;
    /** @var EntityManagerInterface */
    private $manager;
    /** @var SiteManagerService */
    private $detector;

    public function __construct(EntityManagerInterface $manager, SiteManagerService $detector)
    {
        $this->menuEntries = [];
        $this->manager     = $manager;
        $this->detector    = $detector;
    }

    /**
     * @param MenuElement $element
     * @param string      $alias
     */
    public function add(MenuElement $element, $alias = 'default_menu')
    {
        $this->menuEntries[$alias][] = $element;
    }

    /**
     * @param $menuName
     *
     * @return MenuElement[]
     */
    public function getMenu($menuName)
    {
        $menus = $this->menuEntries;

        if (array_key_exists($menuName, $this->menuEntries)) {
            return $menus[$menuName];
        }

        $site = $this->detector->getSite();

        $menu = $this->manager->getRepository(Menu::class)->findOneBy(
            ['site' => $site->getId() ? $site : null, 'name' => $menuName]
        );

        if ($menu) {
            return $menu->getRootElements();
        }

        return [];
    }

    /** @{@inheritdoc} */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->menuEntries);
    }

    /** @{@inheritdoc} */
    public function offsetGet($offset)
    {
        return $this->menuEntries[$offset];
    }

    /** @{@inheritdoc} */
    public function offsetSet($offset, $value)
    {
        $this->menuEntries[$offset] = $value;
    }

    /** @{@inheritdoc} */
    public function offsetUnset($offset)
    {
        $this->menuEntries[$offset] = [];
    }
}
