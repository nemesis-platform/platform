<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.08.2014
 * Time: 12:04
 */

namespace NemesisPlatform\Core\Account\Menu\Profile;

use NemesisPlatform\Core\CMS\Entity\MenuElement;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ManagePhonesLink extends MenuElement
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

        $this->setLabel('Телефоны');
        $this->setLink($router->generate('site_preferences_manage_phones'));
        $this->setTitle('Управление списком телефонов');

        if (!$tokenStorage->getToken() || !$authorizationChecker->isGranted('ROLE_USER')) {
            $this->setDisabled(true);

            return;
        }

        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $tokenStorage->getToken()->getUser();

        if (!$user->getPhone() || !$user->getPhone()->isConfirmed()) {
            $this->setBadge('danger', false, 'exclamation-triangle');
        }
    }
}
