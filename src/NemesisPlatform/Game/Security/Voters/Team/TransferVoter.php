<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.12.2014
 * Time: 12:01
 */

namespace NemesisPlatform\Game\Security\Voters\Team;

use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Team;
use NemesisPlatform\Game\Security\Voters\TransferVoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;

class TransferVoter extends AbstractVoter implements TransferVoterInterface
{

    /**
     * Return an array of supported classes. This will be called by supportsClass
     *
     * @return array an array of supported classes, i.e. array('Acme\DemoBundle\Model\Product')
     */
    protected function getSupportedClasses()
    {
        return ['array'];
    }

    /**
     * Return an array of supported attributes. This will be called by supportsAttribute
     *
     * @return array an array of supported attributes, i.e. array('CREATE', 'READ')
     */
    protected function getSupportedAttributes()
    {
        return [
            self::IS_INVITED,
            self::IS_MEMBER,
            self::IS_REQUESTING,
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
     * @param array                $object
     * @param UserInterface|string $user
     *
     * @return bool
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        if (!array_key_exists('team', $object)) {
            return false;
        }
        if (!array_key_exists('data', $object)) {
            return false;
        }

        /** @var \NemesisPlatform\Game\Entity\Team $team */
        $team = $object['team'];
        /** @var \NemesisPlatform\Game\Entity\Participant $data */
        $data = $object['data'];

        if (!($team instanceof Team)) {
            return false;
        }
        if (!($data instanceof Participant)) {
            return false;
        }

        switch ($attribute) {
            case self::IS_REQUESTING:
                return $team->getRequests()->contains($data);
            case self::IS_INVITED:
                return $team->getInvites()->contains($data);
            case self::IS_MEMBER:
                return $team->getMembers()->contains($data);
        }

        return false;
    }
}
