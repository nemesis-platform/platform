<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.03.2015
 * Time: 16:17
 */

namespace NemesisPlatform\Game\Entity\Rule\Participant;

use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Rule\AbstractRuleEntity;

class SingleTeamRule extends AbstractRuleEntity
{
    protected $description = 'Ограничение на одну команду в сезоне';

    public function isStrict()
    {
        return true;
    }

    /**
     * @param $subject
     *
     * @return bool True if subject can be checked with rule, false otherwise
     */
    public function isApplicable($subject)
    {
        return $subject instanceof Participant;
    }

    /** @return string */
    public function getType()
    {
        return 'rule_logic_single_team';
    }

    /**
     * @param $subject \NemesisPlatform\Game\Entity\Participant
     * @param $context
     *
     * @return bool
     */
    protected function check($subject, $context)
    {
        return count($subject->getTeams()) <= 1;
    }
}
