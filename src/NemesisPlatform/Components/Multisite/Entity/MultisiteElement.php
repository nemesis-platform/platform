<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 28.05.2015
 * Time: 16:16
 */

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
