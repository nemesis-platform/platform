<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.03.2015
 * Time: 14:34
 */

namespace NemesisPlatform\Game\Entity\Rule;

trait RuleCheckerTrait
{
    public function checkRules($subject, $context)
    {
        foreach ($this->getRules() as $rule) {
            if ($rule->isEnabled() && $rule->isApplicable($subject)) {
                if ($rule->isStrict() && !$rule->isValid($subject, $context)) {
                    return false;
                }
            }
        }

        return true;
    }

    /** @return RuleInterface[] */
    abstract public function getRules();
}
