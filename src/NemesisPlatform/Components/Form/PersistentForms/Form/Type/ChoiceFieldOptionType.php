<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.06.2015
 * Time: 13:08
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ChoiceFieldOptionType extends AbstractType
{
    /** {@inheritdoc} */
    public function getName()
    {
        return 'choice_field_option';
    }

    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('key', 'text', ['required' => false]);
        $builder->add('value', 'text', ['required' => true]);
        $builder->add('optgroup', 'text', ['required' => false]);
    }
}
