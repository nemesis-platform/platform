<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 13.04.2015
 * Time: 12:37
 */

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
