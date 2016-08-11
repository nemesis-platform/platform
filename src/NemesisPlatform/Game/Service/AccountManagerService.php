<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.07.2015
 * Time: 15:29
 */

namespace NemesisPlatform\Game\Service;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Components\MultiSite\Service\SiteProviderInterface;
use NemesisPlatform\Game\Entity\Team;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AccountManagerService
{
    /** @var  SiteProviderInterface */
    private $siteManager;
    /** @var  EntityManagerInterface */
    private $manager;
    /** @var  TokenStorageInterface */
    private $tokenStorage;

    /**
     * AccountManagerService constructor.
     *
     * @param SiteProviderInterface  $siteManager
     * @param EntityManagerInterface $manager
     * @param TokenStorageInterface  $tokenStorage
     */
    public function __construct(
        SiteProviderInterface $siteManager,
        EntityManagerInterface $manager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->siteManager  = $siteManager;
        $this->manager      = $manager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return \NemesisPlatform\Game\Entity\Team[]
     */
    public function getRequests()
    {
        $seasons = $this->siteManager->getSite()->getSeasons()->toArray();

        $requests = $this->manager->getRepository(
            Team::class
        )->getRequestes($this->tokenStorage->getToken()->getUser(), $seasons);

        return $requests;
    }

    /**
     * @return Team[]
     */
    public function getInvites()
    {
        $seasons = $this->siteManager->getSite()->getSeasons()->toArray();

        $invites = $this->manager->getRepository(
            Team::class
        )->getInvites(
            $this->tokenStorage->getToken()->getUser(),
            $seasons
        );

        return $invites;
    }
}
