<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.02.2015
 * Time: 17:27
 */

namespace NemesisPlatform\Game\Entity\Rule\Team;

use NemesisPlatform\Game\Entity\Rule\AlertRuleEntity;
use NemesisPlatform\Game\Entity\Team;

class ValidationPassNotificationRule extends AlertRuleEntity
{

    protected $description = 'Уведомление о комплектации команды';
    protected $message
        = '
        На данный момент команда соответствует всем требованиям сайта.
        Команда сформирована в {{ datetime }}
        ';
    protected $urgency = self::URGENCY_SUCCESS;

    /**
     * @param \NemesisPlatform\Game\Entity\Team $subject
     *
     * @return string
     */
    public function getRenderedMessage($subject = null)
    {
        $message = $this->getMessage();

        return str_replace('{{ datetime }}', $subject->getFormDate()->format('Y.m.d H:i:s'), $message);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'rule_team_validation_pass_notification';
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
        return $subject->getFormDate() === null;
    }
}
