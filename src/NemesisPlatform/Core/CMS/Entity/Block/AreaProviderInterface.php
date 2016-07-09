<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 07.07.2015
 * Time: 11:04
 */

namespace NemesisPlatform\Core\CMS\Entity\Block;

interface AreaProviderInterface
{
    /**
     * @return string[]
     */
    public function getAreas();
}
