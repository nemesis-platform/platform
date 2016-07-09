<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.03.2015
 * Time: 16:09
 */

namespace NemesisPlatform\Game\Entity\Rule\Participant;

use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Rule\FixableRuleEntity;
use NemesisPlatform\Game\Entity\Rule\RuleContainerInterface;

class CreateTeamAbilityNotification extends FixableRuleEntity
{
    /** @var  string */
    protected $callToFixMessage = 'Создать команду';
    /** @var  string */
    protected $fixRouteName = 'team_create';
    protected $urgency = self::URGENCY_INFO;
    protected $message = 'У вас есть возможность создать команду';
    protected $description = 'Возможность создать команду';

    /**
     * @param Participant $subject
     *
     * @return array
     */
    public function getFixRouteParams($subject = null)
    {
        return ['season' => $subject->getSeason()->getId()];
    }

    /**
     * @param $subject
     *
     * @return bool True if subject can be checked with rule, false otherwise
     */
    public function isApplicable($subject)
    {
        return $subject instanceof Participant;
    }

    /** @return string */
    public function getType()
    {
        return 'rule_notification_create_team_ability';
    }

    /**
     * @param $subject Participant
     * @param $context
     *
     * @return bool
     */
    protected function check($subject, $context)
    {
        if (!($context instanceof RuleContainerInterface)) {
            throw new \InvalidArgumentException('Context should implement RuleContainerInterface');
        }

        if ($subject->getTeams()->isEmpty()) {
            return false;
        }

        if ($subject->getTeams()->count() === 1) {
            foreach ($context->getRules() as $rule) {
                if ($rule instanceof SingleTeamRule && $rule->isEnabled()) {
                    return true;
                }
            }
        }

        return false;
    }
}
