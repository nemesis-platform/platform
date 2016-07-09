<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 29.09.2014
 * Time: 15:23
 */

namespace NemesisPlatform\Admin\Form\Type;

use NemesisPlatform\Game\Entity\League;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LeagueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, ['label' => 'Название лиги']);
        $builder->add(
            'with_combined',
            'checkbox',
            [
                'label'    => ' + Сборная категория команд',
                'required' => false,
                'attr'     => ['align_with_widget' => true],
            ]
        );
        $builder->add(
            'categories',
            'collection',
            [
                'label'              => 'Категории',
                'type'               => 'user_category_type',
                'allow_add'          => true,
                'allow_delete'       => true,
                'by_reference'       => false,
            ]
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => League::class,
                'attr'       => ['style' => 'horizontal'],
            ]
        );
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'league_type';
    }
}
