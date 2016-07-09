<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.06.2015
 * Time: 15:12
 */

namespace NemesisPlatform\Modules\Game\QAGame\Menu;

use NemesisPlatform\Core\CMS\Entity\MenuElement;
use Symfony\Component\Routing\RouterInterface;

class QAGameAdminMenu extends MenuElement
{
    public function __construct(RouterInterface $router, MenuElement $mMenu)
    {
        parent::__construct();

        $this->setTitle('Игровой модуль анкетного вида');
        $this->setLabel('Модуль QA игры');

        $mMenu->addChild($this);
        $this->setParent($mMenu);

        $this->setLink($router->generate('module_qa_game_admin_dashboard'));
    }
}
