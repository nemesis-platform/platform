<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 12.03.2015
 * Time: 13:22
 */

namespace NemesisPlatform\Modules\Game\Core\Form\Type;

use NemesisPlatform\Modules\Game\Core\Entity\Round\Round;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoundType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', ['label' => 'Название']);
        $builder->add(
            'active',
            'choice',
            ['label' => 'Статус', 'choices' => [true => 'Активный', false => 'Не активный']]
        );
        $builder->add('season', 'site_seasons', ['label' => 'Сезон']);
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Round::class,
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
        return 'game_core_round';
    }
}
