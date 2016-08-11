<?php

namespace NemesisPlatform\Components\MultiSite\Entity;

interface MultiSiteElement
{
    /**
     * @param SiteInterface $site
     *
     * @return bool
     */
    public function belongsToSite(SiteInterface $site);
}
