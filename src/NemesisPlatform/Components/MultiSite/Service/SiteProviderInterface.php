<?php

namespace NemesisPlatform\Components\MultiSite\Service;

use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;

interface SiteProviderInterface
{
    /** @return SiteInterface */
    public function getSite();
}
