<?php
namespace NemesisPlatform\Game\Service;

use NemesisPlatform\Components\Themes\Entity\ThemeInstance;

interface ThemedSiteInterface
{
    /**
     * @return string
     */
    public function getTheme();

    /**
     * @return null|ThemeInstance
     */
    public function getThemeInstance();
}
