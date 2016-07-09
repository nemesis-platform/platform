<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.06.2015
 * Time: 14:37
 */

namespace NemesisPlatform\Modules\Game\QAGame\Entity;

use DateTime;
use NemesisPlatform\Modules\Game\Core\Entity\Decision;
use NemesisPlatform\Modules\Game\Core\Entity\Round\DecisionRoundInterface;
use NemesisPlatform\Modules\Game\Core\Entity\Round\Round;
use NemesisPlatform\Modules\Game\Core\Entity\Round\TimedRoundInterface;

class QARound extends Round implements DecisionRoundInterface, TimedRoundInterface
{
    /** @var  QuestionList */
    private $questionList;
    /** @var  \DateTime */
    private $start;
    /** @var  \DateTime */
    private $finish;

    /**
     * QARound constructor.
     */
    public function __construct()
    {
        $this->start  = new DateTime();
        $this->finish = new DateTime();
    }

    /**
     * @return QuestionList
     */
    public function getQuestionList()
    {
        return $this->questionList;
    }

    /**
     * @param QuestionList $questionList
     */
    public function setQuestionList($questionList)
    {
        $this->questionList = $questionList;
    }


    /** @return Decision */
    public function createDecision()
    {
        return new QADecision($this->questionList);
    }

    /** @return bool */
    public function isDecisionAvailable()
    {
        return $this->isStarted() && !$this->isFinished();
    }

    /** @return bool */
    public function isStarted()
    {
        $now = new DateTime();

        return $now > $this->start;
    }

    /** @return bool */
    public function isFinished()
    {
        $now = new DateTime();

        return $now > $this->finish;
    }

    /** @return DateTime */
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

    /** @return DateTime */
    public function getFinish()
    {
        return $this->finish;
    }

    /**
     * @param DateTime $finish
     */
    public function setFinish($finish)
    {
        $this->finish = $finish;
    }

    /** @return bool */
    public function isPaused()
    {
        return false;
    }
}
