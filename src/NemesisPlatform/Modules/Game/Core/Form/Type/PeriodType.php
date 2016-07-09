<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-31
 * Time: 21:58
 */

namespace NemesisPlatform\Modules\Game\Core\Form\Type;

use NemesisPlatform\Modules\Game\Core\Entity\Period;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('position', 'number', ['label' => '#']);
        $builder->add(
            'start',
            'datetime',
            [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'label'       => 'Начало',
            ]
        );
        $builder->add(
            'end',
            'datetime',
            [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'label'       => 'Конец',
            ]
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Period::class,
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
        return 'period_type';
    }
}
