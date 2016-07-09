<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 12.12.2014
 * Time: 18:24
 */

namespace NemesisPlatform\Game\Security\Voters\Team;

use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Team;
use NemesisPlatform\Game\Security\Voters\TeamVoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;

class ParticipantTeamVoter extends AbstractVoter implements TeamVoterInterface
{

    /**
     * Return an array of supported classes. This will be called by supportsClass
     *
     * @return array an array of supported classes, i.e. array('Acme\DemoBundle\Model\Product')
     */
    protected function getSupportedClasses()
    {
        return [Team::class];
    }

    /**
     * Return an array of supported attributes. This will be called by supportsAttribute
     *
     * @return array an array of supported attributes, i.e. array('CREATE', 'READ')
     */
    protected function getSupportedAttributes()
    {
        return [
            self::TEAM_REQUEST,
            self::TEAM_REVOKE_REQUEST,
            self::TEAM_ACCEPT_INVITE,
            self::TEAM_DECLINE_INVITE,
            self::TEAM_LEAVE,
        ];
    }

    /**
     * Perform a single access check operation on a given attribute, object and (optionally) user
     * It is safe to assume that $attribute and $object's class pass supportsAttribute/supportsClass
     * $user can be one of the following:
     *   a UserInterface object (fully authenticated user)
     *   a string               (anonymously authenticated user)
     *
     * @param string               $attribute
     * @param Team                 $team
     * @param UserInterface|string $user
     *
     * @return bool
     */
    protected function isGranted($attribute, $team, $user = null)
    {
        if (!$user instanceof User) {
            return false;
        }

        if (!$team->getSeason()->isRegistrationOpen()) {
            return false;
        }

        $participant = $user->getParticipation($team->getSeason());

        if (!$participant) {
            return false;
        }

        switch ($attribute) {
            case self::TEAM_REQUEST:
                return
                    !$team->getMembers()->contains($participant)
                    && !$team->isFrozen() && $team->isAbleToRequest($user);
            case self::TEAM_REVOKE_REQUEST:
                return $team->getRequests()->contains($participant);
            case self::TEAM_ACCEPT_INVITE:
                return
                    $team->getInvites()->contains($participant)
                    && !$team->getMembers()->contains($participant)
                    && !$team->hasMaxMembers()
                    && !$team->isFrozen()
                    && $team->isAbleToJoin($user);
            case self::TEAM_DECLINE_INVITE:
                return $team->getInvites()->contains($participant);
            case self::TEAM_LEAVE:
                return
                    ($team->getCaptain()->getUser() !== $user)
                    && $team->getMembers()->contains($participant)
                    && !$team->isFrozen()
                    && $team->isAbleToLeave($user);
        }

        return false;
    }
}
