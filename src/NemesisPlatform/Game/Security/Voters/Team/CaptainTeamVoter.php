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
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;

class CaptainTeamVoter extends AbstractVoter implements TeamVoterInterface
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
            self::TEAM_MANAGE,
            self::TEAM_DISBAND,
            self::TEAM_INVITE,
            self::TEAM_REVOKE_INVITE,
            self::TEAM_KICK,
            self::TEAM_ACCEPT_REQUEST,
            self::TEAM_DECLINE_REQUEST,
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