<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-27
 * Time: 00:11
 */

namespace NemesisPlatform\Core\CMS\Entity;

use DateTime;
use NemesisPlatform\Core\Account\Entity\User;

class PageRevision
{
    /** @var  int|null */
    private $id;
    /** @var  Page */
    private $page;
    /** @var  \NemesisPlatform\Core\Account\Entity\User */
    private $author;
    /** @var  string */
    private $content;
    /** @var  DateTime */
    private $createdTime;

    public function __construct()
    {
        $this->createdTime = new DateTime();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param \NemesisPlatform\Core\Account\Entity\User $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return DateTime
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    /**
     * @param DateTime $createdTime
     */
    public function setCreatedTime($createdTime)
    {
        $this->createdTime = $createdTime;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param Page $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }
}
