<?php

namespace NemesisPlatform\Components\Form\PersistentForms\Form\Transformer;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\AbstractValue;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 27.05.2015
 * Time: 16:42
 */
class ValueTransformer implements DataTransformerInterface
{
    /** @var  AbstractValue */
    private $value;
    /** @var  string */
    private $propertyPath;

    /**
     * ValueTransformer constructor.
     *
     * @param AbstractValue $value
     * @param string        $propertyPath
     */
    public function __construct(AbstractValue $value, $propertyPath)
    {
        $this->value        = $value;
        $this->propertyPath = $propertyPath;
    }

    /** @inheritdoc */
    public function transform($value)
    {
        if (!($value instanceof AbstractValue)) {
            return null;
        }

        $accessor = new PropertyAccessor();

        return $accessor->getValue($value, $this->propertyPath);
    }

    /** @inheritdoc */
    public function reverseTransform($value)
    {
        $accessor = new PropertyAccessor();
        $accessor->setValue($this->value, $this->propertyPath, $value);

        return $this->value;

    }

}
