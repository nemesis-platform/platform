<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 28.05.2015
 * Time: 11:53
 */

namespace NemesisPlatform\Game\Form\Type;

use NemesisPlatform\Components\MultiSite\Service\SiteManagerInterface;
use NemesisPlatform\Core\Account\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class MemberDescriptionFormType extends AbstractType
{
    /** @var  TokenStorageInterface */
    private $tokenStorage;
    /** @var SiteManagerInterface */
    private $siteManager;

    /**
     * MemberDescriptionFormType constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param SiteManagerInterface  $siteManager
     */
    public function __construct(TokenStorageInterface $tokenStorage, SiteManagerInterface $siteManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->siteManager  = $siteManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException('Team description is allowed only for registered users');
        }
        /** @var \NemesisPlatform\Game\Entity\SeasonedSite $site */
        $site = $this->siteManager->getSite();
        if (!$user->getParticipation($site->getActiveSeason())) {
            throw new AccessDeniedException('Team description is allowed only for active season participants');
        }

        $participant = $user->getParticipation($site->getActiveSeason());

        if (count($participant->getTeams()) === 0) {
            throw new AccessDeniedException('No teams to describe');
        }

        $team = $participant->getTeams()[0];

        $description = $builder->create(
            'description',
            'form',
            [
                'label' => false,
                'attr'  => ['style' => 'horizontal', 'widget_col' => 12],

            ]
        );

        foreach ($team->getMembers() as $member) {
            if ($member === $participant) {
                continue;
            }

            $description->add(
                $member->getId(),
                'text',
                [
                    'attr'  => ['label_col' => 3, 'widget_col' => 9],
                    'label' => $member->getUser()->getFormattedName('%l %sf. %sm.'),
                ]
            );
        }

        $builder->add($description);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'team_members_description';
    }
}
