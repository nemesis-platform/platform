<?php

namespace NemesisPlatform\Components\MultiSite\Service;

use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;

interface SiteFactoryInterface
{
    /**
     * @param array $options
     *
     * @return SiteInterface
     */
    public function createSite(array $options = array());
}
