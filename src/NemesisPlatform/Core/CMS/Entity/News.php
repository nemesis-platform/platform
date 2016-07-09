<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 11.02.14
 * Time: 18:10
 */

namespace NemesisPlatform\Core\CMS\Entity;

use DateTime;
use NemesisPlatform\Components\MultiSite\Entity\MultiSiteElement;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Game\Entity\Season;

/**
 * Class News
 *
 * @package Entity
 */
class News implements MultiSiteElement
{
    const TYPE_DEFAULT  = 0;
    const TYPE_DEFERRED = 1;
    const TYPE_DISABLED = 2;

    /** @var int|null */
    private $id;

    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $body;
    /** @var DateTime */
    private $date;
    /** @var Season */
    private $season;
    /** @var  SiteInterface */
    private $site;

    /** @var int */
    private $type = self::TYPE_DEFAULT;
    /** @var  string */
    private $imageLink;

    public function __construct()
    {
        $this->date = new DateTime();
    }

    /**
     * @return string
     */
    public function getImageLink()
    {
        return $this->imageLink;
    }

    /**
     * @param string $imageLink
     */
    public function setImageLink($imageLink)
    {
        $this->imageLink = $imageLink;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
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
     * @return string
     */
    public function getTitle()
    {
        return strip_tags($this->title);
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
    public function getID()
    {
        return $this->id;
    }

    public function isDeferred()
    {
        return $this->type === self::TYPE_DEFERRED;
    }

    public function isVisible()
    {
        $now = new DateTime();

        return ($this->type === self::TYPE_DEFERRED && $this->date->getTimestamp() < $now->getTimestamp())
        || ($this->type === self::TYPE_DEFAULT);
    }

    public function isDisabled()
    {
        return $this->type === self::TYPE_DISABLED;
    }

    /** {@inheritdoc} */
    public function belongsToSite(SiteInterface $site)
    {
        return $this->getSeason()->getSite() === $site;
    }

    /**
     * @return Season
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param Season $season
     */
    public function setSeason($season)
    {
        $this->season = $season;
    }
}
