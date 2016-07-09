<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 12.03.2015
 * Time: 13:22
 */

namespace NemesisPlatform\Modules\Game\Core\Form\Type;

use NemesisPlatform\Modules\Game\Core\Entity\Round\PeriodicRound;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PeriodicRoundType extends RoundType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'game_core_periodic_round';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $round = $event->getData();
                $form  = $event->getForm();

                if (!$round || !$round->getId()) {
                    return;
                }

                $form->add(
                    'periods',
                    'collection',
                    [
                        'label'              => 'Периоды',
                        'type'               => 'period_type',
                        'allow_add'          => true,
                        'allow_delete'       => true,
                        'by_reference'       => false,
                    ]
                );
            }
        );
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => PeriodicRound::class,
            ]
        );
    }
}
