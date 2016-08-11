<?php
namespace NemesisPlatform\Core\CMS\Theme;

use NemesisPlatform\Components\MultiSite\Service\SiteProviderInterface;
use NemesisPlatform\Components\Skins\Service\ConfigurableThemeInterface;
use NemesisPlatform\Components\Skins\Service\LayoutStorageInterface;
use NemesisPlatform\Components\Skins\Service\SkinProviderInterface;
use NemesisPlatform\Components\Skins\Service\SkinRegistryInterface;
use NemesisPlatform\Core\CMS\Entity\NemesisSite;

class SiteThemeService implements SkinProviderInterface
{
    /**
     * @var SiteProviderInterface
     */
    private $siteManager;
    /**
     * @var SkinRegistryInterface
     */
    private $skinRegistry;

    /** @var  LayoutStorageInterface|null|false */
    private $theme;

    /**
     * SiteThemeService constructor.
     *
     * @param SiteProviderInterface $siteProvider
     * @param SkinRegistryInterface $skinRegistry
     */
    public function __construct(SiteProviderInterface $siteProvider, SkinRegistryInterface $skinRegistry)
    {
        $this->siteManager  = $siteProvider;
        $this->skinRegistry = $skinRegistry;
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
        if (null === $this->theme) {
            /** @var NemesisSite $site */
            $site = $this->siteManager->getSite();

            $theme = $theme = $this->skinRegistry->get($site->getTheme());

            if (($instance = $site->getThemeInstance()) !== null) {
                /** @var ConfigurableThemeInterface $theme */
                $theme = $this->skinRegistry->get($instance->getTheme());
                if ($theme instanceof ConfigurableThemeInterface) {
                    $theme->configure($instance->getConfig());
                }
            }

            $this->theme = $theme;
        }
    }

    /**
     * @return false|null|LayoutStorageInterface
     */
    public function getTheme()
    {
        $this->init();

        return $this->theme;
    }

    public function __call($name, $arguments)
    {
        $this->init();

        return call_user_func_array([$this->theme, $name], $arguments);
    }
}
