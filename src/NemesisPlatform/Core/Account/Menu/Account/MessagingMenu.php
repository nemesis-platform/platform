<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.08.2014
 * Time: 15:07
 */

namespace NemesisPlatform\Core\Account\Menu\Account;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Core\CMS\Entity\MenuElement;
use NemesisPlatform\Core\Account\Entity\AbstractMessage;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MessagingMenu extends MenuElement
{
    public function __construct(
        RouterInterface $router,
        EntityManagerInterface $manager,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct();

        if (!$tokenStorage->getToken() || !$authorizationChecker->isGranted('ROLE_USER')) {
            $this->setDisabled(true);

            return;
        }

        $this->setLabel('Личные сообщения');
        $this->setTitle('Личные сообщения');
        $this->setLink($router->generate('messaging_list'));

        $messages = $manager->getRepository(AbstractMessage::class)->findBy(
            ['recipient' => $tokenStorage->getToken()->getUser(), 'read' => false]
        );

        if (count($messages)) {
            $this->setBadge(count($messages) ? 'success' : 'info', true, count($messages));
        }
    }
}
