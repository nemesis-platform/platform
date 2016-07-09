<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.05.2015
 * Time: 11:10
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\AbstractValue;

class TextValue extends AbstractValue
{
    private $textValue;

    public function setValue($value)
    {
        $this->setTextValue($value);
    }

    public function getRenderValue()
    {
        return $this->getValue();
    }

    public function getValue()
    {
        return $this->getTextValue();
    }

    /**
     * @return mixed
     */
    public function getTextValue()
    {
        return $this->textValue;
    }

    /**
     * @param mixed $textValue
     */
    public function setTextValue($textValue)
    {
        $this->textValue = $textValue;
    }
}
