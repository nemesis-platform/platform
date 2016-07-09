<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 28.05.2015
 * Time: 11:37
 */

namespace NemesisPlatform\Game\Entity\Field;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\ArrayValue;
use NemesisPlatform\Components\Form\PersistentForms\Form\Transformer\ValueTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormTypeInterface;

class TeamField extends AbstractField
{
    /**
     * @return string Name key for the object
     */
    public function getType()
    {
        return 'team_description';
    }

    /**
     * @return string|FormTypeInterface
     */
    protected function getRenderedFormType()
    {
        return 'team_members_description';
    }


    /**
     * @return DataTransformerInterface
     */
    protected function getValueTransformer()
    {
        $value = new ArrayValue();
        $value->setField($this);

        return new ValueTransformer($value, 'value');
    }
}
