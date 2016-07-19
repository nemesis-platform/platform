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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TransferVoter extends Voter implements TransferVoterInterface
{
    /** {@inheritdoc} */
    protected function supports($attribute, $subject)
    {
        return
            is_array($subject) &&
            array_key_exists('data', $subject) &&
            $subject['data'] instanceof Participant &&
            array_key_exists('team', $subject) &&
            $subject['team'] instanceof Team &&
            in_array($attribute, $this->getSupportedAttributes(), true);
    }

    private function getSupportedAttributes()
    {
        return [
            self::IS_INVITED,
            self::IS_MEMBER,
            self::IS_REQUESTING,
        ];
    }

    /** {@inheritdoc} */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Team $team */
        $team = $subject['team'];
        /** @var Participant $participant */
        $participant = $subject['data'];

        switch ($attribute) {
            case self::IS_REQUESTING:
                return $team->getRequests()->contains($participant);
            case self::IS_INVITED:
                return $team->getInvites()->contains($participant);
            case self::IS_MEMBER:
                return $team->getMembers()->contains($participant);
        }

        return false;
    }
}
