<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.12.2014
 * Time: 16:47
 */

namespace NemesisPlatform\Modules\Game\Core\Security;

use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Core\Account\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class DecisionVoter implements VoterInterface
{
    const MAKE_DECISION = 'game_core.make_decision';


    /**
     * Checks if the voter supports the given class.
     *
     * @param string $class A class name
     *
     * @return bool true if this Voter can process the class
     */
    public function supportsClass($class)
    {
        return in_array(SiteInterface::class, class_implements($class), true);
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token      A TokenInterface instance
     * @param object|null    $site       The object to secure
     * @param array          $attributes An array of attributes associated with the method being invoked
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $site, array $attributes)
    {
        $user = $token->getUser();

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                return self::ACCESS_ABSTAIN;
            }
        }

        if (!($site instanceof SiteInterface)) {
            return self::ACCESS_ABSTAIN;
        }


        if (!($user instanceof User)) {
            return self::ACCESS_ABSTAIN;
        }

        $season = $site->getActiveSeason();
        if (!$season) {
            return self::ACCESS_DENIED;
        }

        $seasonData = $user->getParticipation($season);
        if (!$seasonData) {
            return self::ACCESS_DENIED;
        }


        if ($seasonData->getTeams()->isEmpty()) {
            return self::ACCESS_DENIED;
        }

        foreach ($attributes as $attribute) {
            switch ($attribute) {
                case self::MAKE_DECISION:
                    return self::ACCESS_GRANTED;
            }
        }

        return self::ACCESS_DENIED;
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
        return in_array($attribute, [self::MAKE_DECISION], true);
    }
}
