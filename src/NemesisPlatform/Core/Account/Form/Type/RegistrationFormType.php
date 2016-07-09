<?php
/**
 * Created by PhpStorm.
 * User: scaytrase
 * Date: 19.12.2014
 * Time: 22:15
 */

namespace NemesisPlatform\Core\Account\Form\Type;

use NemesisPlatform\Game\Entity\Season;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends AbstractType
{

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'registration_form';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $season = $options['season'];

        $builder
            ->add('account', 'user_type', ['label' => false])
            ->add(
                'participant',
                'participant',
                ['season' => $season, 'label' => false]
            )
            ->add('rules', 'rules_checkbox')
            ->add('submit', 'submit', ['label' => 'Зарегистрироваться', ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(['season']);
        $resolver->setAllowedTypes(['season' => Season::class]);
    }
}
