<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.08.2014
 * Time: 12:09
 */

namespace NemesisPlatform\Core\Account\Menu\Profile;

use NemesisPlatform\Core\CMS\Entity\MenuElement;
use Symfony\Component\Routing\RouterInterface;

class ChangeAvatarLink extends MenuElement
{
    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        parent::__construct();

        $this->setLabel('Аватар');
        $this->setLink($router->generate('site_preferences_avatar'));
        $this->setTitle('Обновить аватар');
    }
}
