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
use NemesisPlatform\Components\Form\PersistentForms\Form\Transformer\ValueTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormTypeInterface;

class ChoiceField extends AbstractField
{
    /** @var  array */
    private $choices;
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
     * @return string Name key for the object
     */
    public function getType()
    {
        return 'choice_field';
    }


    /**
     * @return array
     */
    public function getChoices()
    {
        $choices = array();

        foreach ($this->choices as $key => $choice) {
            if (!is_array($choice)) {
                $choices[$key] = array('value'=>$choice);
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
        $this->choices = array();

        foreach ($choices as $key => $choice) {
            if (!is_array($choice)) {
                $choice = array('value' => $choice);
            }

            if (array_key_exists('key', $choice) && $choice['key'] !== null) {
                $key = $choice['key'];
            }

            $this->choices[$key] = $choice;
        }
    }

    /**
     * @return FormTypeInterface|string FormTypeInterface instance or string which represents registered form type
     */
    public function getFormType()
    {
        return 'field_choice_settings';
    }

    /**
     * @return string|FormTypeInterface
     */
    protected function getRenderedFormType()
    {
        return 'choice';
    }

    /**
     * @return array
     */
    protected function getRenderedFormOptions()
    {
        $choices = array();

        foreach ($this->choices as $key => $row) {
            if (!is_array($row)) {
                $row = array('value' => $row);
            }

            if (array_key_exists('key', $row) && $row['key'] !== null) {
                $key = $row['key'];
            }

            if (array_key_exists('optgroup', $row) && $row['optgroup'] !== null) {
                $choices[$row['optgroup']][$key] = $row['value'];
            } else {
                $choices[$key] = $row['value'];
            }
        }

        return array_replace_recursive(
            parent::getRenderedFormOptions(),
            array(
                'choices'  => $choices,
                'expanded' => $this->expanded,
                'multiple' => $this->multiple,
            )
        );

    }

    /**
     * @return DataTransformerInterface
     */
    protected function getValueTransformer()
    {
        $value = new ChoiceValue();
        $value->setField($this);

        return new ValueTransformer($value, 'value');
    }
}
