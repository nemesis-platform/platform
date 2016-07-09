<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.06.2014
 * Time: 13:20
 */

namespace NemesisPlatform\Core\Account\Entity;

use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;

class ConfirmationSMS implements ShortMessageInterface
{
    private $template;
    private $code;
    private $recipient;

    public function __construct($recipient, $code, $template = "Code: %s")
    {
        $this->code      = $code;
        $this->template  = $template;
        $this->recipient = $recipient;
    }

    /**
     * Get Message Body
     *
     * @return string message to be sent
     */
    public function getBody()
    {
        return urlencode(sprintf($this->template, $this->code));
    }

    /**
     * Get Message Recipient
     *
     * @return string message recipient number
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set Message Recipient
     *
     * @param $recipient string
     *
     * @return void
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }
}
