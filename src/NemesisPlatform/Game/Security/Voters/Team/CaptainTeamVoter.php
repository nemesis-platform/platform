<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 12.12.2014
 * Time: 18:14
 */

namespace NemesisPlatform\Game\Security\Voters\Team;

use NemesisPlatform\Game\Entity\Team;
use NemesisPlatform\Game\Security\Voters\TeamVoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CaptainTeamVoter extends Voter implements TeamVoterInterface
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
            self::TEAM_MANAGE,
            self::TEAM_DISBAND,
            self::TEAM_INVITE,
            self::TEAM_REVOKE_INVITE,
            self::TEAM_KICK,
            self::TEAM_ACCEPT_REQUEST,
            self::TEAM_DECLINE_REQUEST,
        ];
    }

    /** {@inheritdoc} */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        $team = $subject;

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($team->getCaptain()->getUser() !== $user) {
            return false;
        }

        if (!$team->getSeason()->isRegistrationOpen()) {
            return false;
        }

        switch ($attribute) {
            case self::TEAM_MANAGE:
                return !$team->isFrozen() && $team->getSeason()->isRegistrationOpen();
            case self::TEAM_DISBAND:
                return !$team->isFrozen();
            case self::TEAM_ACCEPT_REQUEST:
                return !$team->isFrozen() && !$team->hasMaxMembers();
            case self::TEAM_DECLINE_REQUEST:
                return true;
            case self::TEAM_INVITE:
                return !$team->isFrozen() && !$team->hasMaxMembers();
            case self::TEAM_REVOKE_INVITE:
                return true;
            case self::TEAM_KICK:
                return !$team->isFrozen();
        }

        return false;
    }
}
