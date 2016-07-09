<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.03.2015
 * Time: 11:20
 */

namespace NemesisPlatform\Modules\Game\Core\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Modules\Game\Core\Entity\Period;
use NemesisPlatform\Modules\Game\Core\Entity\Round\DraftRound;
use NemesisPlatform\Modules\Game\Core\Entity\Round\Round;
use NemesisPlatform\Game\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class AbstractDecisionFormType extends AbstractType
{
    /** @var  EntityManagerInterface */
    protected $manager;
    /** @var  TokenStorageInterface */
    protected $tokenStorage;

    public function __construct(EntityManagerInterface $manager, TokenStorageInterface $tokensStorage)
    {
        $this->manager      = $manager;
        $this->tokenStorage = $tokensStorage;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'csrf_protection' => true,
                ]
            )
            ->setOptional(['period'])
            ->setRequired(
                ['team', 'round']
            )
            ->setAllowedTypes(
                [
                    'period' => [Period::class, 'null'],
                    'round'  => Round::class,
                    'team'   => Team::class,
                ]
            );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var DraftRound $round */
        $round               = $options['round'];
        $team                = $options['team'];
        $view->vars['team']  = $team;
        $view->vars['round'] = $round;
    }
}
