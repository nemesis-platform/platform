<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 14.08.2014
 * Time: 16:31
 */

namespace NemesisPlatform\Core\Account\Entity;

use DateTime;

class AbstractMessage
{
    /** @var  int|null */
    private $id;
    /** @var  NamedUserInterface */
    private $recipient;
    /** @var  string */
    private $body;
    /** @var  DateTime */
    private $created;
    /** @var null|AbstractMessage */
    private $parent_message = null;
    /** @var bool */
    private $read = false;

    public function __construct()
    {
        $this->created = new DateTime();
    }

    /**
     * @return boolean
     */
    public function isRead()
    {
        return $this->read;
    }

    /**
     * @param boolean $read
     */
    public function setRead($read)
    {
        $this->read = $read;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return AbstractMessage|null
     */
    public function getParentMessage()
    {
        return $this->parent_message;
    }

    /**
     * @param AbstractMessage|null $parent_message
     */
    public function setParentMessage($parent_message)
    {
        $this->parent_message = $parent_message;
    }

    /**
     * @return NamedUserInterface
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param NamedUserInterface $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }
}
