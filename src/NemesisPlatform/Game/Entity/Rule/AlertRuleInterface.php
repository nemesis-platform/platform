<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 25.02.2015
 * Time: 17:24
 */

namespace NemesisPlatform\Game\Entity\Rule;

interface AlertRuleInterface
{
    /** @return string */
    public function getMessage();

    /** @return string */
    public function getUrgency();
}
