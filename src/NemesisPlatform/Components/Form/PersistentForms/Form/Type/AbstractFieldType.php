<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 29.09.2014
 * Time: 17:57
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Form\Type;


use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AbstractFieldType extends AbstractType
{
    private $classname;

    /**
     * AbstractFieldType constructor.
     *
     * @param $classname
     */
    public function __construct($classname = AbstractField::class)
    {
        $this->classname = $classname;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('label' => 'Код поля'));
        $builder->add('title', 'text', array('label' => 'Описание'));
        $builder->add('required', 'checkbox', array('required' => false));
        $builder->add('help_message', 'textarea', array('label' => 'Подсказка', 'required' => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => $this->classname));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'field_settings';
    }
}
