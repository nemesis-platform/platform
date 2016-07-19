<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 23.10.2014
 * Time: 12:39
 */

namespace NemesisPlatform\Game\Repository;

use DateTime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use NemesisPlatform\Game\Entity\Team;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TeamListener
{
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $team = $args->getEntity();

        if ($team instanceof Team) {
            $this->updateTeam($team);
        }
    }

    /**
     * @param Team $team
     *
     * @return Team
     */
    public function updateTeam(Team $team)
    {
        if ($team->getFormDate() && !$team->getSeason()->checkRules($team, $team->getSeason())) {
            $team->setFormDate(null);
        }

        if (!$team->getFormDate() && $team->getSeason()->checkRules($team, $team->getSeason())) {
            $team->setFormDate(new DateTime());
        }

        if (!$team->getPersistentTag()) {
            $team->setPersistentTag(sha1(uniqid('team_')));
        }

        return $team;
    }
}
