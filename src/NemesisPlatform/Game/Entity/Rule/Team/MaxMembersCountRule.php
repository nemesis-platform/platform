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

class MaxMembersCountRule extends AlertRuleEntity
{
    protected $description = 'Проверка максимального числа участников в команде';
    protected $message = 'В вашей команде {{ count }} участников. Максимум по правилам - {{ max }} участников.';
    protected $urgency = self::URGENCY_DANGER;

    /** @var  int */
    private $max;

    /**
     * @param Team $subject
     *
     * @return string
     */
    public function getRenderedMessage($subject = null)
    {
        $message = $this->getMessage();
        $message = str_replace('{{ count }}', $subject->getMembers()->count(), $message);
        $message = str_replace('{{ max }}', $this->max, $message);

        return $message;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /** @return FormTypeInterface */
    public function getFormType()
    {
        return 'rule_form_team_max_members_count';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'rule_team_max_members_count';
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
     * @param $subject Team
     * @param $context
     *
     * @return bool
     */
    protected function check($subject, $context)
    {
        return $subject->getMembers()->count() <= $this->max;
    }
}
