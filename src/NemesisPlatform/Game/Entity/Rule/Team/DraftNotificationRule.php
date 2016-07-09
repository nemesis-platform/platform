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

class DraftNotificationRule extends AlertRuleEntity
{

    protected $description = 'Уведомление о идентификаторе жеребьевки';
    protected $message = 'Идентификатор вашей команды - {{ draft }}';
    protected $urgency = self::URGENCY_INFO;

    /**
     * @param Team $subject
     *
     * @return string
     */
    public function getRenderedMessage($subject = null)
    {
        $message = $this->getMessage();

        return str_replace('{{ draft }}', substr($subject->getPersistentTag(), 0, 6), $message);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'rule_team_draft_notification';
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
        return $subject->getPersistentTag() === null;
    }
}
