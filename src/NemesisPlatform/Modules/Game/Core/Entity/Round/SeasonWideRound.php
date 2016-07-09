<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 15.05.2015
 * Time: 13:46
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\Round;

use NemesisPlatform\Game\Entity\Team;

class SeasonWideRound extends Round implements FilteredRoundInterface
{

    /**
     * @param \NemesisPlatform\Game\Entity\Team $team
     *
     * @return bool
     */
    public function hasTeam(Team $team)
    {
        return $team->getSeason() && $team->getSeason() === $this->getSeason();
    }

    /**
     * @return \NemesisPlatform\Game\Entity\Team[]
     */
    public function getTeams()
    {
        // TODO: Implement getTeams() method.
    }
}
