<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 15.01.2015
 * Time: 15:18
 */

namespace NemesisPlatform\Components\Themes\Entity;

class ThemeInstance
{
    /** @var  int|null */
    private $id;
    /** @var  array */
    private $config;
    /** @var  string */
    private $description;
    /** @var  string */
    private $theme;

    /**
     * @return string identifier for instance asset generation
     */
    public function getAssetsKey()
    {
        return $this->theme.'_i'.$this->id;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }
}
