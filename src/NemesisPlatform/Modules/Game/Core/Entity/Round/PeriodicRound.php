<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.03.2015
 * Time: 15:52
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\Round;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Modules\Game\Core\Entity\Period;

/**
 * Class PeriodicRound
 *
 * @package NemesisPlatform\Modules\Game\Core\Entity
 */
class PeriodicRound extends DraftRound implements TimedRoundInterface
{
    /** @var Period[]|ArrayCollection */
    protected $periods;

    public function __construct()
    {
        parent::__construct();
        $this->periods = new ArrayCollection();
    }

    /**
     * @return DateTime|null
     */
    public function getStart()
    {
        if (count($this->periods) === 0) {
            return null;
        }

        $datetime = min(
            array_map(
                function (Period $period) {
                    return $period->getStart();
                },
                $this->periods->toArray()
            )
        );

        return $datetime;
    }

    /**
     * @return DateTime|null
     */
    public function getFinish()
    {
        if (count($this->periods) === 0) {
            return null;
        }

        $datetime = max(
            array_map(
                function (Period $period) {
                    return $period->getEnd();
                },
                $this->periods->toArray()
            )
        );

        return $datetime;
    }

    /**
     * @param Period $period
     */
    public function addPeriod(Period $period)
    {
        if (!$this->periods->contains($period)) {
            $this->periods->add($period);
            $period->setRound($this);
        }
    }

    /**
     * @param Period $period
     */
    public function removePeriod(Period $period)
    {
        if ($this->periods->contains($period)) {
            $this->periods->removeElement($period);
            $period->setRound(null);
        }
    }

    /** @return bool */
    public function isPaused()
    {
        return $this->isStarted() && !$this->isFinished() && $this->getCurrentPeriod();
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        $result  = false;
        $curDate = new DateTime('now');
        foreach ($this->getPeriods() as $period) {
            if ($curDate > $period->getStart()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * @return Period[]|ArrayCollection
     */
    public function getPeriods()
    {
        return $this->periods;
    }

    /**
     * @param Period[] $periods
     */
    public function setPeriods($periods)
    {
        $this->periods = $periods;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        $result  = true;
        $curDate = new DateTime('now');
        foreach ($this->getPeriods() as $period) {
            if ($curDate < $period->getEnd()) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /** Period */
    public function getCurrentPeriod()
    {
        $currentPeriod = null;
        $curDate       = new DateTime('now');
        foreach ($this->getPeriods() as $period) {
            if ($curDate > $period->getStart() && $curDate < $period->getEnd()) {
                $currentPeriod = $period;
                break;
            }
        }

        return $currentPeriod;
    }
}
