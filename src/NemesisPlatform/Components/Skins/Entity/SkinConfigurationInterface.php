<?php

namespace NemesisPlatform\Components\Skins\Entity;

interface SkinConfigurationInterface extends \ArrayAccess
{
    /** @return array */
    public function getConfiguration();

    /** @return string */
    public function getTheme();
}
