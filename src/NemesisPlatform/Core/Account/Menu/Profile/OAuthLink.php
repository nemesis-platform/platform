<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.12.2014
 * Time: 15:07
 */

namespace NemesisPlatform\Core\Account\Menu\Profile;

use NemesisPlatform\Core\CMS\Entity\MenuElement;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class OAuthLink extends MenuElement
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

        if (!$tokenStorage->getToken() || !$authorizationChecker->isGranted('ROLE_USER')) {
            $this->setDisabled(true);

            return;
        }

        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $tokenStorage->getToken()->getUser();

        if (!$user->hasConfirmedNumbers()) {
            $this->setDisabled(true);

            return;
        }

        $this->setLink(
            $router->generate('oauth_preferences_list_providers')
        );
        $this->setTitle('Управление привязанными аккаунтами');
        $this->setLabel('Внешние аккаунты');
    }
}
