<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.02.2015
 * Time: 14:09
 */

namespace NemesisPlatform\Admin\Service\Generator;

class GeneratorRegistry
{
    /** @var  EntityGeneratorInterface[]| */
    private $generators = [];

    /** @return EntityGeneratorInterface[]| */
    public function all()
    {
        return $this->generators;
    }
}
