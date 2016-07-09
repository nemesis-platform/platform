<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 23.09.2014
 * Time: 16:18
 */

namespace NemesisPlatform\Core\Account\Menu;

use NemesisPlatform\Core\CMS\Entity\MenuElement;

class DividerMenuEntry extends MenuElement
{
    public function __construct()
    {
        parent::__construct();
        $this->setType(MenuElement::TYPE_DELIMITER);
    }
}
