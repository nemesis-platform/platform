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
use Symfony\Component\OptionsResolver\OptionsResolver;

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

    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', ['label' => 'Код поля']);
        $builder->add('title', 'text', ['label' => 'Описание']);
        $builder->add('required', 'checkbox', ['required' => false]);
        $builder->add('help_message', 'textarea', ['label' => 'Подсказка', 'required' => false]);
    }

    /** {@inheritdoc} */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => $this->classname]);
    }

    /** {@inheritdoc} */
    public function getName()
    {
        return 'field_settings';
    }
}
