<?php

namespace NemesisPlatform\Components\Skins\Service;

interface SkinProviderInterface
{
    /**
     * @param string      $layout
     * @param string|null $fallback
     *
     * @return string
     */
    public function getLayout($layout = 'base', $fallback = null);

    /**
     * @return false|null|LayoutStorageInterface
     */
    public function getTheme();
}
