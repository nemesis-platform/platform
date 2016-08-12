<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-05-25
 * Time: 21:49
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\ChoiceValue;
use NemesisPlatform\Components\Form\PersistentForms\Form\Type\ChoiceFieldConfigurationType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChoiceField extends AbstractField
{
    /** @var  array */
    private $choices = [];
    /** @var bool */
    private $expanded = false;
    /** @var bool */
    private $multiple = false;

    /**
     * @return boolean
     */
    public function isExpanded()
    {
        return $this->expanded;
    }

    /**
     * @param boolean $expanded
     */
    public function setExpanded($expanded)
    {
        $this->expanded = $expanded;
    }

    /**
     * @return boolean
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * @param boolean $multiple
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        $choices = [];

        foreach ($this->choices as $key => $choice) {
            if (!is_array($choice)) {
                $choices[$key] = ['value' => $choice];
            } else {
                $choices[$key] = $choice;
            }
        }

        return $choices;
    }

    /**
     * @param array $choices
     */
    public function setChoices(array $choices)
    {
        foreach ($choices as $key => $value) {
            $this->choices[$key] = $value;
        }
        $this->choices = $choices;
    }

    public function getViewFormOptions()
    {
        $choices = [];

        foreach ($this->choices as $key => $row) {
            if (!is_array($row)) {
                $row = ['value' => $row];
            }

            if (array_key_exists('key', $row) && $row['key'] !== null) {
                $key = $row['key'];
            }

            if (array_key_exists('optgroup', $row) && $row['optgroup'] !== null) {
                $choices[$row['optgroup']][$row['value']] = $key;
            } else {
                $choices[$row['value']] = $key;
            }
        }

        return array_replace_recursive(
            parent::getViewFormOptions(),
            [
                'choices_as_values' => true,
                'choices'           => $choices,
                'expanded'          => $this->expanded,
                'multiple'          => $this->multiple,
            ]
        );
    }

    public function getViewForm()
    {
        return ChoiceType::class;
    }

    public function getConfigurationForm()
    {
        return ChoiceFieldConfigurationType::class;
    }

    public function getDataClass()
    {
        return ChoiceValue::class;
    }
}
