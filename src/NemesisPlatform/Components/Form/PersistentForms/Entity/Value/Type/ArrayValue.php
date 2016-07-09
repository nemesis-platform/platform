<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 29.05.2015
 * Time: 12:32
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\AbstractValue;

class ArrayValue extends AbstractValue
{
    /** @var array */
    private $arrayValue;

    /**
     * @param array $arrayValue
     */
    public function setValue($arrayValue)
    {
        $this->arrayValue = (array) $arrayValue;
    }

    /**
     * @return array
     */
    public function getRenderValue()
    {
        return $this->getValue();
    }

    /**
     * @return array
     */
    public function getValue()
    {
        return $this->arrayValue;
    }
}
