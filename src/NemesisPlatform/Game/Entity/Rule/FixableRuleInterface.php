<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 25.02.2015
 * Time: 17:25
 */

namespace NemesisPlatform\Game\Entity\Rule;

interface FixableRuleInterface
{
    /** @return string */
    public function getCallToFixMessage();

    /** @return string */
    public function getFixRouteName();

    /**
     * @param mixed|null $subject
     *
     * @return array
     */
    public function getFixRouteParams($subject = null);
}
