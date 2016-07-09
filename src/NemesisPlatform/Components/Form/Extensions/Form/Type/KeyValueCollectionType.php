<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.07.2014
 * Time: 15:09
 */

namespace NemesisPlatform\Components\Form\Extensions\Form\Type;


use NemesisPlatform\Components\Form\Extensions\Form\DataTransformer\DataToKeyValueArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeyValueCollectionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new DataToKeyValueArrayTransformer());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('type' => 'key_value_entry'));
    }

    public function getParent()
    {
        return 'collection';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'key_value_collection';
    }
}
