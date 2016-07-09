<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.02.2015
 * Time: 18:53
 */

namespace NemesisPlatform\Game\Twig\Extension;

use NemesisPlatform\Game\Entity\Rule\RuleInterface;

class RuleCheckerExtension extends \Twig_Extension
{

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'nemesis_rule_checker_extension';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('check_ruleset', [$this, 'check']),
        ];
    }

    /**
     * @param $subject mixed
     * @param $ruleset RuleInterface[]
     * @param $context mixed
     *
     * @return bool
     */
    public function check($subject, $ruleset, $context)
    {
        foreach ($ruleset as $rule) {
            if ($rule->isStrict() && $rule->isEnabled() && $rule->isApplicable($subject)) {
                if (!$rule->isValid($subject, $context)) {
                    return false;
                }
            }
        }

        return true;
    }
}
