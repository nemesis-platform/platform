<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 29.09.2014
 * Time: 13:59
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Value;


use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;

abstract class AbstractValue
{
    /** @var  int|null */
    private $id;
    /** @var  AbstractField */
    private $field;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
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

    abstract public function getValue();

    abstract public function setValue($value);

    abstract public function getRenderValue();
}
