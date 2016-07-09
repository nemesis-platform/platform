<?php
/**
 * Created by PhpStorm.
 * User: scaytrase
 * Date: 19.12.2014
 * Time: 20:13
 */

namespace NemesisPlatform\Core\Account\Security\Voters;

use NemesisPlatform\Components\MultiSite\Service\SiteManagerService;
use NemesisPlatform\Game\Entity\SeasonedSite;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AnonymousVoter implements UserVoterInterface, VoterInterface
{

    /** @var SiteManagerService */
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

        if ($user instanceof UserInterface) {
            return self::ACCESS_ABSTAIN;
        }

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                return self::ACCESS_ABSTAIN;
            }
        }

        $site = $this->siteManager->getSite();

        if (!$site || !$site->getId()) {
            return self::ACCESS_ABSTAIN;
        }

        if ($site instanceof SeasonedSite) {
            $season = $site->getActiveSeason();
            if (!$season) {
                return self::ACCESS_ABSTAIN;
            }

            if (!$season->isRegistrationOpen()) {
                return self::ACCESS_ABSTAIN;
            }
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
        return in_array($attribute, [self::USER_REGISTER]);
    }
}
