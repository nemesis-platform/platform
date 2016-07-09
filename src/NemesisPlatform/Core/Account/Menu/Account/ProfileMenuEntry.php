<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-08-17
 * Time: 22:55
 */

namespace NemesisPlatform\Core\Account\Menu\Account;

use NemesisPlatform\Core\CMS\Entity\MenuElement;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProfileMenuEntry extends MenuElement
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

        $error = false;

        foreach ($user->getParticipations() as $participant) {
            if (!$participant->getSeason()->checkRules($participant, $participant->getSeason())) {
                $error = true;
            }
        }

        if ($error) {
            $this->setBadge('danger', false, 'exclamation-triangle');
        }

        $this->setParent(null);
        $this->setLabel('Профиль');
        $this->setTitle('Обзор профиля');

        $this->setLink($router->generate('site_user_preferences'));
    }
}
