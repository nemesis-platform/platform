<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.12.2014
 * Time: 15:32
 */

namespace NemesisPlatform\Core\Account\Security\Voters;

use NemesisPlatform\Core\Account\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter implements VoterInterface, UserVoterInterface
{

    /**
     * Checks if the voter supports the given class.
     *
     * @param string $class A class name
     *
     * @return bool true if this Voter can process the class
     */
    public function supportsClass($class)
    {
        return true;
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token      A TokenInterface instance
     * @param object|null    $object     The object to secure
     * @param array          $attributes An array of attributes associated with the method being invoked
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return self::ACCESS_ABSTAIN;
        }

        if (!$user instanceof User) {
            return self::ACCESS_ABSTAIN;
        }

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                return self::ACCESS_ABSTAIN;
            }

            switch ($attribute) {
                case self::USER_UPDATE_ESSENTIALS:
                    return $user->isFrozen() ? self::ACCESS_DENIED : self::ACCESS_GRANTED;
                case self::USER_UPDATE_MISC:
                    return self::ACCESS_GRANTED;
                case self::USER_CONFIRM_PHONE:
                    return self::ACCESS_GRANTED;
                case self::USER_SWITCH_PHONE:
                    return self::ACCESS_GRANTED;
                case self::USER_CHANGE_PASSWORD:
                    return self::ACCESS_GRANTED;
            }
        }

        return self::ACCESS_ABSTAIN;
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
        return in_array(
            $attribute,
            [
                self::USER_UPDATE_ESSENTIALS,
                self::USER_UPDATE_MISC,
                self::USER_CHANGE_PASSWORD,
                self::USER_CONFIRM_PHONE,
                self::USER_SWITCH_PHONE,
            ]
        );
    }
}
