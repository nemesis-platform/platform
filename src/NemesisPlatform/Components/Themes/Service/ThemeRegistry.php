<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.08.2014
 * Time: 10:53
 */

namespace NemesisPlatform\Components\Themes\Service;

use NemesisPlatform\Components\Themes\Entity\ThemeInstance;

class ThemeRegistry
{

    /** @var  ThemeInterface[] */
    private $themes = [];

    /**
     * @param ThemeInstance|string $type
     * @param string               $layout
     *
     * @return null|string
     */
    public function getTemplate($type, $layout = 'base')
    {
        $instance = null;

        if ($type instanceof ThemeInstance) {
            $instance = $type;
            $type     = $instance->getTheme();
        }

        if (!array_key_exists($type, $this->themes)) {
            return null;
        }

        $theme = $this->themes[$type];

        if ($theme instanceof ConfigurableThemeInterface && $instance) {
            $theme->setConfiguration($instance->getConfig());
        }

        return $theme->get($layout);
    }

    /**
     * @param $key string
     *
     */
    public function get($key)
    {
        if (!array_key_exists($key, $this->themes)) {
            throw new \LogicException($key.' is not found in the registry');
        }

        return $this->themes[$key];
    }

    public function add($name, ThemeInterface $theme)
    {
        $this->themes[$name] = $theme;
    }

    public function all()
    {
        return $this->themes;
    }

    public function has($alias)
    {
        return array_key_exists($alias, $this->themes);
    }
}
