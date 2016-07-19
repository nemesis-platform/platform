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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ParticipantTeamVoter extends Voter implements TeamVoterInterface
{

    /** {@inheritdoc} */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Team && in_array($attribute, $this->getSupportedAttributes(), true);
    }

    /**
     * Return an array of supported attributes. This will be called by supportsAttribute
     *
     * @return array an array of supported attributes, i.e. array('CREATE', 'READ')
     */
    private function getSupportedAttributes()
    {
        return [
            self::TEAM_REQUEST,
            self::TEAM_REVOKE_REQUEST,
            self::TEAM_ACCEPT_INVITE,
            self::TEAM_DECLINE_INVITE,
            self::TEAM_LEAVE,
        ];
    }

    /** {@inheritdoc} */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        $team = $subject;

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
