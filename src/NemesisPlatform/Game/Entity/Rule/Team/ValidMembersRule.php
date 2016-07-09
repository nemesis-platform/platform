<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.02.2015
 * Time: 13:11
 */

namespace NemesisPlatform\Game\Entity\Rule\Team;

use InvalidArgumentException;
use NemesisPlatform\Game\Entity\Rule\AlertRuleEntity;
use NemesisPlatform\Game\Entity\Rule\RuleContainerInterface;
use NemesisPlatform\Game\Entity\Team;

class ValidMembersRule extends AlertRuleEntity
{
    protected $description = 'Проверка состава команды';
    protected $message = "В вашей команде есть участники, которые не соответствуют правилам сезона";
    protected $urgency = self::URGENCY_DANGER;

    /**
     * @param $subject
     *
     * @return bool True if subject can be checked with rule, false otherwise
     */
    public function isApplicable($subject)
    {
        return ($subject instanceof Team);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'rule_team_valid_members';
    }

    /**
     * @param Team $subject
     * @param null $context Checking context
     *
     * @return bool True if isValid is successful, false otherwise
     */
    protected function check($subject, $context)
    {
        if (!($context instanceof RuleContainerInterface)) {
            throw new InvalidArgumentException('RuleContainerInterface is expected as context');
        }

        foreach ($subject->getMembers() as $participant) {
            foreach ($context->getRules() as $rule) {
                if ($rule->isStrict() && $rule->isEnabled()) {
                    if ($rule->isApplicable($participant)
                        && (!$rule->isValid($participant))
                    ) {
                        return false;
                    }

                    if ($rule->isApplicable($participant->getUser())
                        && (!$rule->isValid($participant->getUser()))
                    ) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
