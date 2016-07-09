<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.06.2015
 * Time: 13:40
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TableFieldType extends AbstractFieldType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add(
            'fields',
            'collection',
            array(
                'type'    => 'entity',
                'allow_add'    => true,
                'allow_delete' => true,
                'options' => array(
                    'class' => 'NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField'
                ),
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'table_field_settings';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array('data_class' => 'NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\TableField'));
    }

}
