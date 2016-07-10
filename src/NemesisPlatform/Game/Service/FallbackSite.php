<?php
namespace NemesisPlatform\Game\Service;

use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use Symfony\Component\HttpFoundation\Request;

class FallbackSite implements SiteInterface, ThemedSiteInterface
{
    /** @var  string */
    private $url;
    /** @var  string */
    private $shortName;
    /** @var  string */
    private $theme;

    /**
     * FallbackSite constructor.
     *
     * @param $url
     * @param $shortName
     */
    public function __construct($url, $shortName)
    {
        $this->url       = $url;
        $this->shortName = $shortName;
    }

    /** {@inheritdoc} */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param mixed $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /** {@inheritdoc} */
    public function getThemeInstance()
    {
        return null;
    }

    /** @return string base URL */
    public function getDomain()
    {
        return $this->url;
    }

    /** @return string|null favicon URL */
    public function getFavicon()
    {
        return null;
    }

    /** @return string|null Logo URL */
    public function getLogo()
    {
        return null;
    }

    /** @return string long name of the site */
    public function getFullName()
    {
        return $this->shortName;
    }

    /** @return string short name of the site */
    public function getShortName()
    {
        return $this->shortName;
    }

    /** @return string|null contact email */
    public function getContactEmail()
    {
        return null;
    }

    /**
     * @param Request $request
     *
     * @return bool whether the site matches request
     */
    public function match(Request $request)
    {
        return $request->getHost() === $this->url;
    }

    public function __toString()
    {
        return $this->url;
    }
}
