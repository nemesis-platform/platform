<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.02.2015
 * Time: 13:13
 */

namespace NemesisPlatform\Game\Entity\Rule;

interface RuleContainerInterface
{
    /** @param $rules RuleInterface[] */
    public function setRules($rules);

    /** @return RuleInterface[] */
    public function getRules();
}
