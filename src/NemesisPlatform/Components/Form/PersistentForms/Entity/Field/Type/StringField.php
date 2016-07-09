<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-05-25
 * Time: 21:54
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use Symfony\Component\Form\FormTypeInterface;

class StringField extends AbstractField
{
    /**
     * @return string Name key for the object
     */
    public function getType()
    {
        return 'field_text';
    }

    /**
     * @return string|FormTypeInterface
     */
    protected function getRenderedFormType()
    {
        return 'text';
    }
}
