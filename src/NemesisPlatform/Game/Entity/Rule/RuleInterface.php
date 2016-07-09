<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.02.2015
 * Time: 12:44
 */

namespace NemesisPlatform\Game\Entity\Rule;

interface RuleInterface
{
    /**
     * @param      $subject
     * @param null $context Checking context
     *
     * @return bool True if isValid is successful, false otherwise
     */
    public function isValid($subject, $context = null);

    /**
     * @param $subject
     *
     * @return bool True if subject can be checked with rule, false otherwise
     */
    public function isApplicable($subject);

    /** @return bool True if rule passing is essential */
    public function isStrict();

    /** @return bool True if rule is enabled */
    public function isEnabled();
}
