<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.02.2015
 * Time: 12:43
 */

namespace NemesisPlatform\Game\Entity\Rule;

abstract class AlertRuleEntity extends AbstractRuleEntity implements AlertRuleInterface
{
    const URGENCY_SUCCESS = 'success';
    const URGENCY_DEFAULT = 'default';
    const URGENCY_INFO    = 'info';
    const URGENCY_WARNING = 'warning';
    const URGENCY_DANGER  = 'danger';

    private static $urgencyTypes
        = [
            self::URGENCY_SUCCESS,
            self::URGENCY_DEFAULT,
            self::URGENCY_INFO,
            self::URGENCY_WARNING,
            self::URGENCY_DANGER,
        ];

    /** @var string */
    protected $message;

    /** @var string */
    protected $urgency;

    public static function getUrgencyTypes()
    {
        return self::$urgencyTypes;
    }

    /**
     * @param $subject
     *
     * @return string
     */
    public function getRenderedMessage($subject = null)
    {
        return $this->getMessage();
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getUrgency()
    {
        return $this->urgency;
    }

    /**
     * @param string $urgency
     */
    public function setUrgency($urgency)
    {
        if (!in_array($urgency, self::$urgencyTypes)) {
            throw new \InvalidArgumentException('Invalid urgency type');
        }

        $this->urgency = $urgency;
    }
}
