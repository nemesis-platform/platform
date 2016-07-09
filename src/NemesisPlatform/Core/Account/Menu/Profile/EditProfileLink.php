<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.08.2014
 * Time: 12:33
 */

namespace NemesisPlatform\Core\Account\Menu\Profile;

use NemesisPlatform\Core\CMS\Entity\MenuElement;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class EditProfileLink extends MenuElement
{
    /**
     * @param RouterInterface               $router
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        RouterInterface $router,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct();

        $this->setLabel('Редактировать анкету');
        $this->setLink($router->generate('site_account_edit'));
        $this->setTitle('Редактировать свою анкету');

        if (!$tokenStorage->getToken() || !$authorizationChecker->isGranted('ROLE_USER')) {
            $this->setDisabled(true);

            return;
        }
        if ($authorizationChecker->isGranted('ROLE_FROZEN')) {
            $this->setDisabled(true);

            return;
        }
    }
}
