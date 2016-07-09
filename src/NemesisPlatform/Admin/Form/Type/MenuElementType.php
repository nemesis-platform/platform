<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.05.2014
 * Time: 11:45
 */

namespace NemesisPlatform\Admin\Form\Type;

use NemesisPlatform\Core\CMS\Entity\MenuElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuElementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden', ['read_only' => true, 'disabled' => true, 'label' => 'ID']);
        $builder->add('label', 'text', ['label' => 'Текст']);
        $builder->add('link', 'text', ['label' => 'Ссылка', 'required' => false]);
        $builder->add('title', 'text', ['label' => 'Подсказка', 'required' => false]);
        $builder->add(
            'type',
            'choice',
            [
                'choices' => [
                    MenuElement::TYPE_LINK      => 'Ссылка',
                    MenuElement::TYPE_DROPDOWN  => 'Выпадающее',
                    MenuElement::TYPE_DELIMITER => 'Разделитель',
                ],
                'label'   => 'Тип',
            ]
        );
        $builder->add('classes', 'text', ['label' => 'CSS', 'required' => false]);
        $builder->add(
            'parent',
            'entity_hidden',
            ['class' => MenuElement::class, 'label' => 'Родитель']
        );
        $builder->add('weight', 'hidden', ['label' => 'Вес', 'empty_data' => '255']);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => MenuElement::class]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'menu_element_type';
    }
}
