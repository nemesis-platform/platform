<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.08.2014
 * Time: 11:34
 */

namespace NemesisPlatform\Core\Account\Menu\Profile;

use NemesisPlatform\Core\CMS\Entity\MenuElement;
use Symfony\Component\Routing\RouterInterface;

class ChangePasswordLink extends MenuElement
{
    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        parent::__construct();

        $this->setLink(
            $router->generate('site_service_change_password')
        );
        $this->setTitle('Форма изменения пароля');
        $this->setLabel('Сменить пароль');
    }
}
