<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 04.03.2015
 * Time: 11:07
 */

namespace NemesisPlatform\Modules\Game\Core\Entity;

use NemesisPlatform\Modules\Game\Core\Entity\Round\DraftRound;
use NemesisPlatform\Game\Entity\Team;

class DraftRecord
{
    /** @var  int */
    public $league = 0;
    /** @var  int */
    public $group = 0;
    /** @var  int */
    public $company = 0;
    /** @var  DraftRound */
    private $round;
    /** @var  Team */
    private $team;

    public function __construct(DraftRound $round, Team $team)
    {
        $this->round = $round;
        $this->team  = $team;
    }

    /**
     * @return DraftRound
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * @param DraftRound $round
     */
    public function setRound($round)
    {
        $this->round = $round;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param Team $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }
}
