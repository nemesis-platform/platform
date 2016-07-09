<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.06.2015
 * Time: 16:48
 */

namespace NemesisPlatform\Modules\Game\QAGame\Entity;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;

class DecisionQuestion
{
    /** @var  int|null */
    private $id;
    /** @var  int */
    private $weight = 0;
    /** @var  AbstractField */
    private $field;
    /** @var  QuestionList */
    private $questionList;
    /** @var  string */
    private $title;

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
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return AbstractField
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param AbstractField $field
     */
    public function setField($field)
    {
        $this->field = $field;
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

    public function __toString()
    {
        return sprintf('#%d %s', $this->weight, $this->field->getType());
    }
}
