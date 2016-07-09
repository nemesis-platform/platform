<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-12-20
 * Time: 13:33
 */

namespace NemesisPlatform\Game\Security\Voters\Team;

use NemesisPlatform\Components\MultiSite\Service\SiteManagerService;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Security\Voters\TeamVoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ParticipantVoter implements TeamVoterInterface, VoterInterface
{

    /** @var  SiteManagerService */
    private $siteManager;

    public function __construct(SiteManagerService $siteManager)
    {
        $this->siteManager = $siteManager;
    }

    /**
     * Checks if the voter supports the given class.
     *
     * @param string $class A class name
     *
     * @return bool true if this Voter can process the class
     */
    public function supportsClass($class)
    {
        return in_array($class, [Season::class], true);
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token      A TokenInterface instance
     * @param object|null    $season     The object to secure
     * @param array          $attributes An array of attributes associated with the method being invoked
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $season, array $attributes)
    {
        $user = $token->getUser();

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                return self::ACCESS_ABSTAIN;
            }
        }

        if (!($user instanceof UserInterface)) {
            return self::ACCESS_DENIED;
        }

        if (!($season instanceof Season)) {
            return self::ACCESS_DENIED;
        }

        if (!($user instanceof User)) {
            return self::ACCESS_DENIED;
        }

        $site = $this->siteManager->getSite();

        if (!$site) {
            return self::ACCESS_DENIED;
        }

        if (!in_array($season, $site->getSeasons()->toArray())) {
            return self::ACCESS_DENIED;
        }

        if (!$season->isRegistrationOpen()) {
            return self::ACCESS_DENIED;
        }

        return self::ACCESS_GRANTED;
    }

    /**
     * Checks if the voter supports the given attribute.
     *
     * @param string $attribute An attribute
     *
     * @return bool true if this Voter supports the attribute, false otherwise
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, [self::TEAM_CREATE]);
    }
}
