<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.05.2015
 * Time: 10:02
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\AbstractValue;

class PlainValue extends AbstractValue
{
    /** @var string */
    private $value;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getRenderValue()
    {
        return $this->getValue();
    }
}
