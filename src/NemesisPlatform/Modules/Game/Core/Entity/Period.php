<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-01
 * Time: 17:39
 */

namespace NemesisPlatform\Modules\Game\Core\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Modules\Game\Core\Entity\Round\DraftRound;
use NemesisPlatform\Modules\Game\Core\Entity\Round\PeriodicRound;

class Period
{
    /** @var int */
    private $id = null;
    /** @var DateTime */
    private $start;
    /** @var DateTime */
    private $end;

    /** @var PeriodicRound */
    private $round = null;
    /** @var int */
    private $position = 0;
    /** @var bool */
    private $ratingsPublished = false;
    /** @var bool */
    private $reportsPublished = false;

    /** @var  ArrayCollection|RatingRecord[] */
    private $rating;

    /** @var  ArrayCollection|Report[] */
    private $reports;

    public function __construct()
    {
        $this->start  = new DateTime();
        $this->end    = new DateTime();
        $this->rating = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|Report[]
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @param ArrayCollection|Report[] $reports
     */
    public function setReports($reports)
    {
        $this->reports = $reports;
    }

    /**
     * @return ArrayCollection|RatingRecord[]
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param ArrayCollection|RatingRecord[] $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }


    /** @return boolean */
    public function isReportsPublished()
    {
        return $this->reportsPublished;
    }

    /**
     * @param boolean $reportsPublished
     */
    public function setReportsPublished($reportsPublished)
    {
        $this->reportsPublished = $reportsPublished;
    }

    /**
     * @return boolean
     */
    public function isRatingsPublished()
    {
        return $this->ratingsPublished;
    }

    /**
     * @param boolean $ratingsPublished
     */
    public function setRatingsPublished($ratingsPublished)
    {
        $this->ratingsPublished = $ratingsPublished;
    }

    /**
     * @return DraftRound
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * @param DraftRound|null $round
     */
    public function setRound($round)
    {
        $this->round = $round;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return int
     */
    public function getPeriodLength()
    {
        return $this->getEnd()->getTimestamp() - $this->getStart()->getTimestamp();
    }

    /**
     * @return DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param DateTime $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param DateTime $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @param DateTime|null $fromTime
     *
     * @return int
     */
    public function getRemainingTime($fromTime = null)
    {
        if (!$fromTime) {
            $fromTime = new DateTime();
        }

        return $fromTime->getTimestamp() - $this->getStart()->getTimestamp();
    }

    /**
     * @param DateTime|null $fromTime
     *
     * @return bool|\DateInterval
     */
    public function getRemainingTimeInterval($fromTime = null)
    {
        if (!$fromTime) {
            $fromTime = new DateTime();
        }

        return $this->getEnd()->diff($fromTime);
    }

    /**
     * @param null|DateTime $date
     *
     * @return bool
     */
    public function isRunning($date = null)
    {
        return $this->hasStarted($date) && !$this->hasFinished($date);
    }

    /**
     * @param null|DateTime $date
     *
     * @return bool
     */
    public function hasStarted($date = null)
    {
        if (!$date) {
            $date = new DateTime();
        }

        if ($date->getTimestamp() > $this->getStart()->getTimestamp()) {
            return true;
        }

        return false;
    }

    /**
     * @param null|DateTime $date
     *
     * @return bool
     */
    public function hasFinished($date = null)
    {
        if (!$date) {
            $date = new DateTime();
        }

        if ($date->getTimestamp() < $this->getEnd()->getTimestamp()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Returns previous period by position
     *
     * @return Period|null
     */
    public function getPreviousPeriod()
    {
        /** @var Period[] $periods */
        $periods = $this->round->getPeriods()->filter(
            function (Period $period) {
                return $period->getPosition() < $this->getPosition();
            }
        );

        $maxPosition = null;
        $maxPeriod   = null;

        foreach ($periods as $period) {
            if ($period->getPosition() > $maxPosition) {
                $maxPosition = $period->getPosition();
                $maxPeriod   = $period;
            }
        }

        return $maxPeriod;
    }

    /** @return int */
    public function getPosition()
    {
        return $this->position;
    }

    /** @param int $position */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Returns next period by position
     *
     * @return Period|null
     */
    public function getNextPeriod()
    {
        /** @var Period[] $periods */
        $periods = $this->round->getPeriods()->filter(
            function (Period $period) {
                return $period->getPosition() > $this->getPosition();
            }
        );

        $minPosition = null;
        $minPeriod   = null;

        foreach ($periods as $period) {
            if ($period->getPosition() < $minPosition) {
                $minPosition = $period->getPosition();
                $minPeriod   = $period;
            }
        }

        return $minPeriod;
    }

    public function __toString()
    {
        return sprintf('[%s] %s #%d', $this->round->getId(), $this->round->getName(), $this->position);
    }
}
