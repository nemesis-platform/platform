<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.02.2015
 * Time: 12:43
 */

namespace NemesisPlatform\Game\Entity\Rule;

abstract class FixableRuleEntity extends AlertRuleEntity implements FixableRuleInterface
{
    /** @var  string */
    protected $callToFixMessage;
    /** @var  string */
    protected $fixRouteName;

    /** @return string */
    public function getCallToFixMessage()
    {
        return $this->callToFixMessage;
    }

    /**
     * @param string $callToFixMessage
     */
    public function setCallToFixMessage($callToFixMessage)
    {
        $this->callToFixMessage = $callToFixMessage;
    }

    /** @return string */
    public function getFixRouteName()
    {
        return $this->fixRouteName;
    }

    /**
     * @param string $fixRouteName
     */
    public function setFixRouteName($fixRouteName)
    {
        $this->fixRouteName = $fixRouteName;
    }
}
