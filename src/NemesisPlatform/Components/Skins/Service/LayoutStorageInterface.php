<?php

namespace NemesisPlatform\Components\Skins\Service;

interface LayoutStorageInterface
{
    /**
     * @param string $layout
     *
     * @return string
     */
    public function get($layout = 'base');

    /** @return string[] */
    public function all();
}
