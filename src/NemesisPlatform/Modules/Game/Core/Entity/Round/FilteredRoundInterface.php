<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 15.05.2015
 * Time: 13:36
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\Round;

use NemesisPlatform\Game\Entity\Team;

interface FilteredRoundInterface
{
    /**
     * @param Team $team
     *
     * @return bool
     */
    public function hasTeam(Team $team);

    /**
     * @return Team[]
     */
    public function getTeams();
}
