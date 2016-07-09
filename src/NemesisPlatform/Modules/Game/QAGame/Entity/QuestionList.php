<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.06.2015
 * Time: 16:50
 */

namespace NemesisPlatform\Modules\Game\QAGame\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class QuestionList
{
    /** @var  int|null */
    private $id;
    /** @var  string */
    private $title;
    /** @var  DecisionQuestion[]|ArrayCollection */
    private $questions;

    /**
     * QuestionList constructor.
     */
    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return ArrayCollection|DecisionQuestion[]
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    public function addQuestion(DecisionQuestion $question)
    {
        if (!$this->questions->contains($question)) {
            $this->questions[$question->getField()->getName()] = $question;
            $question->setQuestionList($this);
        }
    }

    public function removeQuestion(DecisionQuestion $question)
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            $question->setQuestionList(null);
        }
    }

    public function __toString()
    {
        return sprintf('[#%d] %s (%d)', $this->id, $this->title, $this->questions->count());
    }
}
