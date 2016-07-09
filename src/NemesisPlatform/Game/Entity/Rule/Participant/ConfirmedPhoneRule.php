<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.02.2015
 * Time: 12:51
 */

namespace NemesisPlatform\Game\Entity\Rule\Participant;

use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Rule\FixableRuleEntity;

class ConfirmedPhoneRule extends FixableRuleEntity
{
    protected $description = 'Наличие телефона у участника';
    protected $message = 'У вас нет подтвержденного номера телефона';
    protected $urgency = self::URGENCY_WARNING;

    /**
     * @param $subject
     *
     * @return bool True if subject can be checked with rule, false otherwise
     */
    public function isApplicable($subject)
    {
        return ($subject instanceof Participant);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'rule_user_confirmed_phone';
    }

    public function getFixRouteName()
    {
        return 'site_preferences_manage_phones';
    }

    /**
     * @param null $subject
     *
     * @return array
     */
    public function getFixRouteParams($subject = null)
    {
        return [];
    }

    /**
     * @param \NemesisPlatform\Game\Entity\Participant $subject
     * @param             $context
     *
     * @return bool
     */
    protected function check($subject, $context)
    {
        return $subject->getUser()->hasConfirmedNumbers();
    }
}
