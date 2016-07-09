<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 14.08.2014
 * Time: 16:34
 */

namespace NemesisPlatform\Core\Account\Entity;

class PrivateMessage extends AbstractMessage
{

    /** @var null|NamedUserInterface */
    private $sender = null;

    /**
     * @return null|NamedUserInterface
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param null|NamedUserInterface $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }
}
