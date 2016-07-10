<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.05.2015
 * Time: 10:04
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\ChoiceField;

class ChoiceValue extends ArrayValue
{
    /**
     * @inheritdoc
     */
    public function getRenderValue()
    {
        /** @var ChoiceField $field */
        $field = $this->getField();

        if (!($field instanceof ChoiceField)) {
            throw new \LogicException('Choice answer belongs to non-choice field');
        }

        $choices = $field->getChoices();

        if ($field->isMultiple()) {
            $values = [];

            foreach ($this->getValue() as $key) {
                if (array_key_exists($key, $choices)) {
                    if (is_array($choices[$key]) && array_key_exists('value', $choices[$key])) {
                        $values[] = $choices[$key]['value'];
                    } else {
                        $values[] = $choices[$key];
                    }
                }
            }

            return $values;
        }

        if (array_key_exists($this->getValue(), $choices)) {
            if (is_array($choices[$this->getValue()]) && array_key_exists('value', $choices[$this->getValue()])) {
                return $choices[$this->getValue()]['value'];
            } else {
                return $choices[$this->getValue()];
            }
        }

        return null;
    }

    /**
     * @return array|mixed|null
     */
    public function getValue()
    {
        /** @var ChoiceField $field */
        $field = $this->getField();
        $value = parent::getValue();

        if ($field->isMultiple()) {
            return $value;
        }

        return array_key_exists(0, $value) ? $value[0] : null;
    }

}
