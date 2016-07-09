<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-05-25
 * Time: 22:07
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChoiceFieldType extends AbstractFieldType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('multiple', 'checkbox', array('required' => false));
        $builder->add('expanded', 'checkbox', array('required' => false));

        $builder->add(
            'choices',
            'collection',
            array(
                'type'         => new ChoiceFieldOptionType(),
                'allow_add'    => true,
                'allow_delete' => true,
            )
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array('data_class' => 'NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\ChoiceField'));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'field_choice_settings';
    }
}
