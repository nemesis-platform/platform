<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 10.06.2015
 * Time: 14:00
 */

namespace NemesisPlatform\Components\Form\Survey\Entity;

use NemesisPlatform\Components\Form\PersistentForms\Entity\FieldInterface;

class SurveyQuestion
{
    /** @var  int|null */
    private $id;
    /** @var int */
    private $weight = 0;
    /** @var  FieldInterface */
    private $field;
    /** @var  Survey */
    private $survey;

    /**
     * @return Survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * @param Survey $survey
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
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
     * @return FieldInterface
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param FieldInterface $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }
}
