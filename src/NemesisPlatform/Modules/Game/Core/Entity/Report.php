<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.06.2014
 * Time: 14:29
 */

namespace NemesisPlatform\Modules\Game\Core\Entity;

use NemesisPlatform\Core\CMS\Entity\File;
use NemesisPlatform\Game\Entity\Team;

class Report extends File
{
    /** @var \NemesisPlatform\Game\Entity\Team */
    private $team;
    /** @var  Period */
    private $period;

    /**
     * @return Period
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param Period $period
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param \NemesisPlatform\Game\Entity\Team $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }
}
