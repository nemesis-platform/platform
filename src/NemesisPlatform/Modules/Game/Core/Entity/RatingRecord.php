<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-01
 * Time: 17:55
 */

namespace NemesisPlatform\Modules\Game\Core\Entity;

use NemesisPlatform\Game\Entity\Team;

class RatingRecord
{
    /** @var  int|null */
    private $id;
    /** @var Team */
    private $team;
    /** @var  Period */
    private $period;
    /** @var  float */
    private $value;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

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
     * @param Team $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
