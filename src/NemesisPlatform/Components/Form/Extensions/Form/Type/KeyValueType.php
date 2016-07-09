<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.07.2014
 * Time: 15:12
 */

namespace NemesisPlatform\Components\Form\Extensions\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeyValueType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('key', 'text');
        $builder->add('value', 'text');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'key_value_entry';
    }
}
