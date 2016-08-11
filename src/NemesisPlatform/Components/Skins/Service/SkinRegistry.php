<?php

namespace NemesisPlatform\Components\Skins\Service;

use NemesisPlatform\Components\Skins\Entity\SkinConfigurationInterface;

final class SkinRegistry implements SkinRegistryInterface
{
    /** @var  LayoutStorageInterface[] */
    private $themes = [];

    /** {@inheritdoc} */
    public function getTemplate($type, $layout = 'base')
    {
        $instance = null;

        if ($type instanceof SkinConfigurationInterface) {
            $instance = $type;
            $type     = $instance->getTheme();
        }

        if (!array_key_exists($type, $this->themes)) {
            return null;
        }

        $theme = $this->themes[$type];

        if ($theme instanceof ConfigurableThemeInterface && $instance) {
            $theme->configure($instance->getConfig());
        }

        return $theme->get($layout);
    }

    /** {@inheritdoc} */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new \OutOfBoundsException($key.' is not found in the registry');
        }

        return $this->themes[$key];
    }

    /** {@inheritdoc} */
    public function has($alias)
    {
        return array_key_exists($alias, $this->themes);
    }

    /** {@inheritdoc} */
    public function add($name, LayoutStorageInterface $theme)
    {
        $this->themes[$name] = $theme;
    }

    /** {@inheritdoc} */
    public function all()
    {
        return $this->themes;
    }
}
