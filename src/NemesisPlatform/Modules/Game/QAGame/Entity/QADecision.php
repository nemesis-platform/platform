<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.06.2015
 * Time: 16:47
 */

namespace NemesisPlatform\Modules\Game\QAGame\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Game\Entity\Team;
use NemesisPlatform\Modules\Game\Core\Entity\Decision;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class QADecision extends Decision
{
    /** @var  DecisionAnswer[]|ArrayCollection */
    private $answers;
    /** @var  QuestionList */
    private $questionList;
    /** @var  QARound */
    private $round;

    /**
     * QADecision constructor.
     *
     * @param Team          $team
     * @param UserInterface $author
     * @param QuestionList  $list
     */
    public function __construct(Team $team, UserInterface $author, QuestionList $list)
    {
        parent::__construct($team, $author);
        $this->answers      = new ArrayCollection();
        $this->questionList = $list;
    }

    /**
     * @return QARound
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * @param QARound $round
     */
    public function setRound($round)
    {
        $this->round = $round;
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

    /**
     * @return ArrayCollection|DecisionAnswer[]
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    public function addAnswer(DecisionAnswer $answer)
    {
        if ($this->questionList->getQuestions()->filter(
            function (DecisionQuestion $question) use ($answer) {
                return $question->getField() === $answer->getValue()->getField();
            }
        )->isEmpty()
        ) {
            return;
        }

        foreach ($this->answers as $storedAnswer) {
            if ($storedAnswer->getValue()->getField() === $answer->getValue()->getField()) {
                $this->answers->removeElement($storedAnswer);
            }
        }

        $this->answers->add($answer);
        $answer->setDecision($this);
    }

    public function removeAnswer(DecisionAnswer $answer)
    {
        if ($answer->getDecision() === $this) {
            $this->answers->removeElement($answer);
            $answer->setDecision(null);
        }
    }

    public function sanitize()
    {
        foreach ($this->answers as $value) {
            if ($this->questionList->getQuestions()->filter(
                function (DecisionQuestion $question) use ($value) {
                    return $question->getField() === $value->getValue()->getField();
                }
            )->isEmpty()
            ) {
                $this->answers->removeElement($value);
                $value->setDecision(null);
            }
        }
    }

    /** @return string Convert decision content to string */
    public function __toString()
    {
        $content = '';

        foreach ($this->answers as $answer) {
            //            $content .= $answer->getValue()->getRenderValue().PHP_EOL;
        }

        return $content;
    }

    /** @return string get decision filename */
    public function getFileName()
    {
        return $this->getTeam()->getName();
    }

    /**
     * @return FormTypeInterface|string FormTypeInterface instance or string which represents registered form type
     */
    public function getFormType()
    {
        return 'qa_decision';
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return !$this->answers->filter(
            function (DecisionAnswer $answer) use ($offset) {
                return $answer->getValue()->getField()->getName() === $offset;
            }
        )->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->answers->filter(
            function (DecisionAnswer $answer) use ($offset) {
                return $answer->getValue()->getField()->getName() === $offset;
            }
        )->first();
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof DecisionAnswer) {
            throw new \InvalidArgumentException('value should be instance of DecisionAnswer');
        }

        if ($value->getValue()->getField()->getName() !== $offset) {
            throw new \InvalidArgumentException('value field should be the same as key');
        }

        $this->answers->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $elements = $this->answers->filter(
            function (DecisionAnswer $answer) use ($offset) {
                return $answer->getValue()->getField()->getName() === $offset;
            }
        );

        foreach ($elements as $element) {
            $this->answers->removeElement($element);
        }
    }
}
