<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 11.03.2015
 * Time: 18:08
 */

namespace NemesisPlatform\Modules\Game\Core\Form\Type;

use NemesisPlatform\Modules\Game\Core\Entity\Scenario\AbstractScenario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractScenarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', ['label' => 'Название']);
        $builder->add('code', 'text', ['label' => 'Код']);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => AbstractScenario::class]
        );
    }
}
