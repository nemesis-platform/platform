<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 25.02.2015
 * Time: 19:02
 */

namespace NemesisPlatform\Game\Entity\Rule\Team;

use NemesisPlatform\Game\Entity\Rule\AlertRuleEntity;
use NemesisPlatform\Game\Entity\Team;
use Symfony\Component\Form\FormTypeInterface;

class MinMembersCountRule extends AlertRuleEntity
{
    protected $description = 'Проверка минимального числа участников в команде';
    protected $message = 'В команде {{ count }} участников. По правилам минимум - {{ min }} участников.';
    protected $urgency = self::URGENCY_DANGER;

    /** @var  int */
    private $min;

    /**
     * @param Team $subject
     *
     * @return string
     */
    public function getRenderedMessage($subject = null)
    {
        $message = $this->getMessage();
        $message = str_replace('{{ count }}', $subject->getMembers()->count(), $message);
        $message = str_replace('{{ min }}', $this->min, $message);

        return $message;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /** @return FormTypeInterface */
    public function getFormType()
    {
        return 'rule_form_team_min_members_count';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'rule_team_min_members_count';
    }

    /**
     * @param $subject
     *
     * @return bool True if subject can be checked with rule, false otherwise
     */
    public function isApplicable($subject)
    {
        return $subject instanceof Team;
    }

    /**
     * @param $subject \NemesisPlatform\Game\Entity\Team
     * @param $context
     *
     * @return bool
     */
    protected function check($subject, $context)
    {
        return $subject->getMembers()->count() >= $this->min;
    }
}
