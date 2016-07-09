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

class AccountMenuEntry extends MenuElement
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

        $this->setParent(null);
        $this->setLabel('Кабинет');
        $this->setTitle('Обзор участия');

        $this->setLink($router->generate('site_account_show'));

        $count = 0;

        foreach ($user->getParticipations() as $sData) {
            foreach ($sData->getTeams() as $team) {
                if ($team->isAbleToManage($user) && (count($team->getRequests()) > 0)) {
                    $count += count($team->getRequests());
                }
            }
        }
        if ($count) {
            $this->setBadge('danger', true, $count);
        }
    }
}
