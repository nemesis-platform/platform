<?php

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Value;

use NemesisPlatform\Components\Form\PersistentForms\Entity\FieldInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\ValueInterface;

abstract class AbstractValue implements ValueInterface
{
    /** @var  int|null */
    private $id;
    /** @var  FieldInterface */
    private $field;

    /**
     * AbstractValue constructor.
     *
     * @param FieldInterface $field
     */
    public function __construct(FieldInterface $field)
    {
        $this->field = $field;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
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
    public function setField(FieldInterface $field)
    {
        $this->field = $field;
    }

    abstract public function setValue($value);

    /** {@inheritdoc} */
    public function getRawValue()
    {
        return $this->getValue();
    }

    abstract public function getValue();

    /** {@inheritdoc} */
    public function getRenderedValue()
    {
        return $this->getRenderValue();
    }

    abstract public function getRenderValue();
}
