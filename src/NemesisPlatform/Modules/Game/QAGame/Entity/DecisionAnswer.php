<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.06.2015
 * Time: 16:48
 */

namespace NemesisPlatform\Modules\Game\QAGame\Entity;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\AbstractValue;

class DecisionAnswer
{
    /** @var  AbstractValue */
    private $value;
    /** @var  QADecision */
    private $decision;

    /**
     * DecisionAnswer constructor.
     *
     * @param AbstractValue $value
     */
    public function __construct(AbstractValue $value)
    {
        $this->value = $value;
    }

    /**
     * @return AbstractValue
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param AbstractValue $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return QADecision
     */
    public function getDecision()
    {
        return $this->decision;
    }

    /**
     * @param QADecision $decision
     */
    public function setDecision($decision)
    {
        $this->decision = $decision;
    }
}
