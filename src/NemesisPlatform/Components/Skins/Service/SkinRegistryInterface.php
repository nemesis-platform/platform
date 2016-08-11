<?php
namespace NemesisPlatform\Components\Skins\Service;

use NemesisPlatform\Core\CMS\Entity\ThemeInstance;

interface SkinRegistryInterface
{
    /**
     * @param ThemeInstance|string $type
     * @param string               $layout
     *
     * @return null|string
     */
    public function getTemplate($type, $layout = 'base');

    /**
     * @param $key string
     *
     * @throws \OutOfBoundsException
     * @return LayoutStorageInterface
     */
    public function get($key);


    /**
     * Register layout storage
     *
     * @param string                 $name
     * @param LayoutStorageInterface $theme
     */
    public function add($name, LayoutStorageInterface $theme);

    /**
     * Retrieve all layouts storage
     *
     * @return LayoutStorageInterface[]
     */
    public function all();

    /**
     * Detects the presence of storage by alias
     *
     * @param string $alias
     *
     * @return bool
     */
    public function has($alias);
}
