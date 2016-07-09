<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 03.07.2015
 * Time: 14:02
 */

namespace NemesisPlatform\Core\CMS\Registry;

use NemesisPlatform\Core\CMS\Entity\Block\BlockInterface;

class BlockTypesRegistry
{
    /** @var  BlockInterface[] */
    private $blocks = [];

    public function all()
    {
        return $this->blocks;
    }

    public function get($alias)
    {
        return $this->blocks[$alias];
    }

    public function add($alias, BlockInterface $block)
    {
        $this->blocks[$alias] = $block;
    }

    public function has($alias)
    {
        return array_key_exists($alias, $this->blocks);
    }
}
