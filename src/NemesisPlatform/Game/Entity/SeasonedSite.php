<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.03.2015
 * Time: 12:49
 */

namespace NemesisPlatform\Game\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Core\CMS\Entity\NemesisSite;

class SeasonedSite extends NemesisSite
{
    /** @var Season[]|ArrayCollection */
    private $seasons;

    /** {@inheritdoc} */
    public function __construct($url, $shortName)
    {
        parent::__construct($url, $shortName);
        $this->seasons  = new ArrayCollection();
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $faviconUrl
     */
    public function setFaviconUrl($faviconUrl)
    {
        $this->faviconUrl = $faviconUrl;
    }

    /**
     * @param string $supportEmail
     */
    public function setSupportEmail($supportEmail)
    {
        $this->supportEmail = $supportEmail;
    }

    /**
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @param string $shortName
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
    }

    /**
     * @param string $logoUrl
     */
    public function setLogoUrl($logoUrl)
    {
        $this->logoUrl = $logoUrl;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getFaviconUrl()
    {
        return $this->faviconUrl;
    }

    /**
     * @return string
     */
    public function getSupportEmail()
    {
        return $this->supportEmail;
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->logoUrl;
    }

    /**
     * @return ArrayCollection|Season[]
     */
    public function getSeasons()
    {
        return $this->seasons;
    }

    /**
     * @param ArrayCollection|Season[] $seasons
     */
    public function setSeasons($seasons)
    {
        $this->seasons = $seasons;
    }

    /**
     * @return Season|null
     */
    public function getActiveSeason()
    {
        foreach ($this->seasons as $s) {
            if ($s->isActive()) {
                return $s;
            }
        }

        return null;
    }

    /**
     * @return bool True if site has season with open registration
     */
    public function isRegistrationOpen()
    {
        if (!$this->isActive()) {
            return false;
        }

        foreach ($this->seasons as $season) {
            if ($season->isRegistrationOpen()) {
                return true;
            }
        }

        return false;
    }
}
