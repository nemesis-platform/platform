<?php
namespace NemesisPlatform\Components\Themes\Service;

use NemesisPlatform\Components\MultiSite\Service\SiteManagerInterface;
use NemesisPlatform\Core\CMS\Entity\NemesisSite;

class SiteThemeService
{
    /**
     * @var SiteManagerInterface
     */
    private $siteManager;

    /**
     * @var ThemeRegistry
     */
    private $themeManager;

    /** @var  ThemeInterface|null|false */
    private $theme = false;

    /**
     * SiteThemeService constructor.
     *
     * @param SiteManagerInterface $siteManager
     * @param ThemeRegistry        $themeManager
     */
    public function __construct(SiteManagerInterface $siteManager, ThemeRegistry $themeManager)
    {
        $this->siteManager  = $siteManager;
        $this->themeManager = $themeManager;
    }

    /**
     * @param string $layout
     *
     * @return string
     */
    public function __invoke($layout = 'base')
    {
        return $this->getLayout($layout);
    }

    /**
     * @param string      $layout
     * @param string|null $fallback
     *
     * @return string
     */
    public function getLayout($layout = 'base', $fallback = null)
    {
        $this->init();

        return $this->theme->get($layout) ?: $fallback;
    }

    protected function init()
    {
        if ($this->theme === false) {
            /** @var NemesisSite $site */
            $site = $this->siteManager->getSite();

            $theme = $theme = $this->themeManager->get($site->getTheme());

            if (($instance = $site->getThemeInstance()) !== null) {
                /** @var ConfigurableThemeInterface $theme */
                $theme = $this->themeManager->get($instance->getTheme());
                if ($theme instanceof ConfigurableThemeInterface) {
                    $theme->setConfiguration($instance->getConfig());
                }
            }
            
            $this->theme = $theme;
        }
    }

    /**
     * @return false|null|ThemeInterface
     */
    public function getTheme()
    {
        $this->init();

        return $this->theme;
    }

    public function __call($name, $arguments)
    {
        $this->init();

        return call_user_func_array(array($this->theme, $name), $arguments);
    }
}
