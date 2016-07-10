<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.03.2015
 * Time: 13:58
 */

namespace NemesisPlatform\Components\MultiSite\Service;

use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;

interface SiteManagerInterface
{
    /** @return SiteInterface */
    public function getSite();
}
