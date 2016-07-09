<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.05.2015
 * Time: 11:03
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\AbstractDecoratedEntitySetField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\AbstractValue;

abstract class AbstractDecoratedEntityValue extends AbstractValue
{
    public function getRenderValue()
    {
        /** @var AbstractDecoratedEntitySetField $field */
        $field = $this->getField();

        if (!($field instanceof AbstractDecoratedEntitySetField)) {
            throw new \LogicException('Entity-set answer belongs to non-entity-set field');
        }

        $list = $field->getChoiceList();

        $values = $list->getValuesForChoices($this->getValue());

        return (count($values) > 0) ? $values[0] : null;
    }
}
