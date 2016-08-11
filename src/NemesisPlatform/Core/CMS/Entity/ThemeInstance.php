<?php

namespace NemesisPlatform\Core\CMS\Entity;

use NemesisPlatform\Components\Skins\Entity\SkinConfigurationInterface;

class ThemeInstance implements SkinConfigurationInterface
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

    /** {@inheritdoc} */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->config);
    }

    /** {@inheritdoc} */
    public function offsetGet($offset)
    {
        return $this->config[$offset];
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value)
    {
        $this->config[$offset] = $value;
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }

    /** {@inheritdoc} */
    public function getConfiguration()
    {
        return $this->config;
    }
}
