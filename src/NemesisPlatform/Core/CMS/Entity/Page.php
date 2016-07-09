<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-26
 * Time: 23:46
 */

namespace NemesisPlatform\Core\CMS\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Components\MultiSite\Entity\MultiSiteElement;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Core\Account\Entity\User;

class Page implements MultiSiteElement
{
    /** @var  int|null */
    private $id;
    /** @var  string */
    private $title;
    /** @var  PageRevision */
    private $lastRevision;
    /** @var  \NemesisPlatform\Core\Account\Entity\User */
    private $author;
    /** @var  DateTime */
    private $createdTime;
    /** @var  string */
    private $alias = '';
    /** @var  PageRevision[]|ArrayCollection */
    private $revisions;
    /** @var  string */
    private $language = 'ru';
    /** @var string */
    private $template = 'layout';
    /** @var  SiteInterface */
    private $site;

    public function __construct()
    {
        $this->createdTime = new DateTime();
        $this->revisions = new ArrayCollection();
    }

    /**
     * @return SiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param SiteInterface $site
     */
    public function setSite(SiteInterface $site)
    {
        $this->site = $site;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template ?: 'layout';
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
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
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return PageRevision
     */
    public function getLastRevision()
    {
        return $this->lastRevision;
    }

    /**
     * @param PageRevision $lastRevision
     */
    public function setLastRevision($lastRevision)
    {
        $this->lastRevision = $lastRevision;
    }

    /**
     * @return ArrayCollection|PageRevision[]
     */
    public function getRevisions()
    {
        return $this->revisions;
    }

    /**
     * @param ArrayCollection|PageRevision[] $revisions
     */
    public function setRevisions($revisions)
    {
        $this->revisions = $revisions;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->lastRevision->getContent();
    }

    /** {@inheritdoc} */
    public function belongsToSite(SiteInterface $site)
    {
        return $this->site === $site;
    }
}
