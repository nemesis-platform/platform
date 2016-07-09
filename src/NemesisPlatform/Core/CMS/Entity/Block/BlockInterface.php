<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 03.07.2015
 * Time: 13:05
 */

namespace NemesisPlatform\Core\CMS\Entity\Block;

interface BlockInterface
{
    /**
     * @param array $options
     *
     * @return string
     */
    public function getTemplate(array $options = []);
}
