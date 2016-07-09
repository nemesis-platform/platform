<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 11.03.2015
 * Time: 14:52
 */

namespace NemesisPlatform\Admin\Menu;

use NemesisPlatform\Core\CMS\Entity\MenuElement;

class ModulesMenu extends MenuElement
{
    public function __construct()
    {
        parent::__construct();

        $this->setParent(null);
        $this->setLabel('Модули');
        $this->setTitle('Игровые модули');
        $this->setIcon('cubes');
        $this->setLink(null);
        $this->setType(MenuElement::TYPE_DROPDOWN);
    }
}
