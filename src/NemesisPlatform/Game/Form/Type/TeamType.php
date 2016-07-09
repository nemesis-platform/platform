<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 20.06.2014
 * Time: 13:59
 */

namespace NemesisPlatform\Game\Form\Type;

use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TeamType extends AbstractType
{
    /** @var  TokenStorageInterface */
    private $tokenStorage;

    /**
     * TeamType constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', ['label' => 'Название']);
        $builder->add('advert', 'textarea', ['label' => 'О команде']);


        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var \NemesisPlatform\Game\Entity\Team $team */
                $team = $event->getData();
                $form = $event->getForm();
                if ($team && $team->getID()) {
                    $form->add(
                        'captain',
                        'entity',
                        [
                            'label'   => 'Капитан',
                            'class' => Participant::class,
                            'choices' => $team->getMembers()->toArray(),
                        ]
                    );
                }
            }
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'empty_data' => function (FormInterface $form) {
                    $season = $form->getConfig()->getOption('season');
                    /** @var User $user */
                    $user = $this->tokenStorage->getToken()->getUser();

                    return new Team(
                        $form->get('name')->getData(),
                        $user->getParticipation($season)
                    );
                },
                'data_class' => Team::class,
            ]
        );
        $resolver->setRequired(['season']);
        $resolver->setAllowedTypes(['season' => [Season::class]]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'team_type';
    }
}
