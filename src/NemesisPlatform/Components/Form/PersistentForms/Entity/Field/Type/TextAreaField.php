<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-05-25
 * Time: 21:59
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type;


use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\TextValue;
use NemesisPlatform\Components\Form\PersistentForms\Form\Transformer\ValueTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormTypeInterface;

class TextAreaField extends AbstractField
{
    /**
     * @return string Name key for the object
     */
    public function getType()
    {
        return 'field_textarea';
    }

    /**
     * @return string|FormTypeInterface
     */
    protected function getRenderedFormType()
    {
        return 'textarea';
    }

    /**
     * @return DataTransformerInterface
     */
    protected function getValueTransformer()
    {
        $value = new TextValue();
        $value->setField($this);

        return new ValueTransformer($value, 'textValue');
    }
}
