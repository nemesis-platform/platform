<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-07-07
 * Time: 23:08
 */

namespace NemesisPlatform\Core\CMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Core\CMS\Entity\Block\AreaProviderInterface;
use NemesisPlatform\Core\CMS\Entity\Block\SiteBlock;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;

class NemesisSite implements AreaProviderInterface, SiteInterface
{
    /** @var  SiteBlock[]|ArrayCollection */
    protected $blocks;
    /** @var string|null */
    protected $theme;
    /** @var ThemeInstance|null */
    protected $themeInstance;
    /** @var  string */
    protected $baseUrl;
    /** @var  string */
    protected $faviconUrl;
    /** @var  string */
    protected $supportEmail;
    /** @var  string */
    protected $fullName;
    /** @var bool */
    protected $active = true;
    /** @var  string */
    protected $shortName;
    /** @var  string */
    protected $logoUrl;
    /** @var string */
    private $id;

    /**
     * Site constructor.
     *
     * @param string $url
     * @param string $shortName
     */
    public function __construct($url, $shortName)
    {
        $this->id        = Uuid::uuid4();
        $this->baseUrl   = $url;
        $this->shortName = $shortName;
        $this->blocks    = new ArrayCollection();
        $this->fullName  = $shortName;
    }

    /**
     * @return ThemeInstance|null
     */
    public function getThemeInstance()
    {
        return $this->themeInstance;
    }

    /**
     * @param ThemeInstance|null $themeInstance
     */
    public function setThemeInstance(ThemeInstance $themeInstance = null)
    {
        $this->themeInstance = $themeInstance;
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * @param null $area
     *
     * @return Block\SiteBlock[]
     */
    public function getBlocks($area = null)
    {
        if ($area === null) {
            return $this->blocks->toArray();
        }

        return $this->blocks->filter(
            function (SiteBlock $block) use ($area) {
                return $block->getArea() === $area;
            }
        )->toArray();
    }

    /**
     * @param SiteBlock $block
     */
    public function addBlock(SiteBlock $block)
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks->add($block);
            $block->setSite($this);
        }
    }

    /**
     * @param SiteBlock $block
     */
    public function removeBlock(SiteBlock $block)
    {
        if ($this->blocks->contains($block)) {
            $this->blocks->removeElement($block);
            $block->setSite(null);
        }
    }

    /**
     * @return string[]
     */
    public function getAreas()
    {
        return ['account'];
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /** @return string base URL */
    public function getDomain()
    {
        return $this->baseUrl;
    }

    /** @return string|null favicon URL */
    public function getFavicon()
    {
        return $this->faviconUrl;
    }

    /** @return string|null Logo URL */
    public function getLogo()
    {
        return $this->logoUrl;
    }

    /** @return string|null contact email */
    public function getContactEmail()
    {
        return $this->supportEmail;
    }

    /**
     * @param Request $request
     *
     * @return bool whether the site matches request
     */
    public function match(Request $request)
    {
        return $request->getHost() === $this->baseUrl;
    }

    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
