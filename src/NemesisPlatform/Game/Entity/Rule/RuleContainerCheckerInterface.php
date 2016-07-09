<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.03.2015
 * Time: 14:33
 */

namespace NemesisPlatform\Game\Entity\Rule;

interface RuleContainerCheckerInterface
{
    public function checkRules($subject, $context);
}
