<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 25.02.2015
 * Time: 17:46
 */

namespace NemesisPlatform\Admin\Form\Type\Rule;

use NemesisPlatform\Game\Entity\Rule\AlertRuleEntity;
use NemesisPlatform\Game\Entity\Rule\FixableRuleEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AbstractRuleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description', 'text', ['label' => 'Описание']);
        $builder->add('strict', 'checkbox', ['required' => false]);
        $builder->add('enabled', 'checkbox', ['required' => false]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $rule = $event->getData();

                if ($rule instanceof AlertRuleEntity) {
                    $form->add('message', 'text');
                    $form->add(
                        'urgency',
                        'choice',
                        [
                            'choices' => array_combine(
                                AlertRuleEntity::getUrgencyTypes(),
                                AlertRuleEntity::getUrgencyTypes()
                            ),
                        ]
                    );
                }

                if ($rule instanceof FixableRuleEntity) {
                    $form->add('callToFixMessage', 'text');
                    $form->add('fixRouteName', 'text');
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => AlertRuleEntity::class]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'rule_form_abstract';
    }
}
