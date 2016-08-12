<?php

namespace NemesisPlatform\Components\Form\Survey\Entity;

use NemesisPlatform\Components\Form\PersistentForms\Entity\ValueInterface;

class SurveyAnswer
{
    /** @var  ValueInterface */
    private $value;
    /** @var  SurveyResult */
    private $parent;

    /**
     * SurveyAnswer constructor.
     *
     * @param ValueInterface $value
     */
    public function __construct(ValueInterface $value)
    {
        $this->value = $value;
    }

    /**
     * @return ValueInterface
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param ValueInterface $value
     */
    public function setValue(ValueInterface $value)
    {
        $this->value = $value;
    }

    /**
     * @return SurveyResult
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param SurveyResult $parent
     */
    public function setParent(SurveyResult $parent)
    {
        $this->parent = $parent;
    }
}
