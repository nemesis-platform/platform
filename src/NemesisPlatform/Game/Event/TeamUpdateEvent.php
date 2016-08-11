<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-10-24
 * Time: 19:48
 */

namespace NemesisPlatform\Game\Event;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Components\MultiSite\Service\SiteProvider;
use NemesisPlatform\Game\Entity\SeasonedSite;
use NemesisPlatform\Game\Repository\TeamListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TeamUpdateEvent
{
    /** @var TokenStorageInterface */
    private $security;
    /** @var EntityManagerInterface */
    private $manager;
    /** @var  SiteProvider */
    private $site_manager;

    public function __construct(
        TokenStorageInterface $security,
        EntityManagerInterface $manager,
        SiteProvider $site_manager
    ) {
        $this->security     = $security;
        $this->manager      = $manager;
        $this->site_manager = $site_manager;
    }

    /**
     * Update user teams on page request
     */
    public function updateUserTeams()
    {
        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return;
        }

        $site = $this->site_manager->getSite();

        if (!$site instanceof SeasonedSite) {
            return;
        }

        $season = $site->getActiveSeason();

        if (!$season) {
            return;
        }

        $sData = $user->getParticipation($season);

        if ($sData) {
            foreach ($sData->getTeams() as $team) {
                (new TeamListener())->updateTeam($team);
            }
        }

        $this->manager->flush();
    }

    /**
     * Get a user from the Security Context
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see Symfony\Component\Security\Core\Authentication\Token\TokenInterface::getUser()
     */
    private function getUser()
    {
        if (null === $token = $this->security->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
}
