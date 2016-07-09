<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 15.05.2015
 * Time: 13:31
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\Round;

use NemesisPlatform\Game\Entity\Season;

class Round
{
    /** @var string */
    private $name;
    /** @var int|null */
    private $id;
    /** @var bool */
    private $active = false;
    /** @var  Season */
    private $season;

    /** @return string */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return \NemesisPlatform\Game\Entity\Season
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param Season $season
     */
    public function setSeason($season)
    {
        $this->season = $season;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $isActive
     */
    public function setActive($isActive)
    {
        $this->active = $isActive;
    }
}
